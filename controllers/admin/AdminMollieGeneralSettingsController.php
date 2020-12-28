<?php
/**
 * Mollie       https://www.mollie.nl
 *
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 *
 * @see        https://github.com/mollie/PrestaShop
 *
 * @license     https://github.com/mollie/PrestaShop/blob/master/LICENSE.md
 * @codingStandardsIgnoreStart
 */

use Mollie\Builder\Content\BaseInfoBlock;
use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Exception\PaymentMethodConfigurationUpdaterException;
use Mollie\Form\Admin\GeneralSettings\FormBuilder\GeneralSettingsFormBuilderInterface;
use Mollie\Form\Admin\GeneralSettings\GeneralSettingsFormDataProvider;
use Mollie\Form\Admin\GeneralSettings\GeneralSettingsFormInterface;
use Mollie\Service\ExceptionService;
use Mollie\Verification\Form\CanSettingFormBeSaved;
use Mollie\Verification\Form\FormSettingVerification;

class AdminMollieGeneralSettingsController extends AbstractAdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->bootstrap = true;
	}

	public function initContent()
	{
		parent::initContent();

		/** @var GeneralSettingsFormBuilderInterface $generalSettingsFormBuilder */
		$generalSettingsFormBuilder = $this->module->getMollieContainer(GeneralSettingsFormBuilderInterface::class);

		$this->content .= $generalSettingsFormBuilder->getForm()->createView();

		$this->context->smarty->assign([
			'content' => $this->content,
		]);
	}

	public function postProcess()
	{
		/** @var BaseInfoBlock $baseInfoBlock */
		$baseInfoBlock = $this->module->getMollieContainer(BaseInfoBlock::class);
		$this->context->smarty->assign($baseInfoBlock->buildParams());

		/** @var GeneralSettingsFormBuilderInterface $generalSettingsFormBuilder */
		$generalSettingsFormBuilder = $this->module->getMollieContainer(GeneralSettingsFormBuilderInterface::class);

		/** @var GeneralSettingsFormInterface $generalSettingsForm */
		$generalSettingsForm = $generalSettingsFormBuilder->getForm();

		$generalSettingsForm->handleRequest(null);

		if ($generalSettingsForm->isSubmitted() && $generalSettingsForm->isValid()) {
			try {
				/** @var FormSettingVerification $canSettingFormBeSaved */
				$canSettingFormBeSaved = $this->module->getMollieContainer(CanSettingFormBeSaved::class);

				if ($canSettingFormBeSaved->verify()) {
					/** @var GeneralSettingsFormDataProvider $generalSettingsFormDataProvider */
					$generalSettingsFormDataProvider = $this->module->getMollieContainer(GeneralSettingsFormDataProvider::class);
					$generalSettingsFormDataProvider->setData($generalSettingsFormDataProvider->getData());
				}
			} catch (FormSettingVerificationException $e) {
				/** @var ExceptionService $exceptionService */
				$exceptionService = $this->module->getMollieContainer(ExceptionService::class);
				$this->errors[] = $exceptionService->getErrorMessageForException(
					$e,
					$exceptionService->getErrorMessages()
				);

				return null;
			} catch (PaymentMethodConfigurationUpdaterException $exception) {
				/** @var ExceptionService $exceptionService */
				$exceptionService = $this->module->getMollieContainer(ExceptionService::class);
				$this->errors[] = $exceptionService->getErrorMessageForException(
					$exception,
					$exceptionService->getErrorMessages(),
					['paymentMethodName' => $exception->getPaymentMethodName()]
				);

				return null;
			}
			$this->confirmations[] = $this->module->l('Successfully updated settings');

			return null;
		}

		return parent::postProcess();
	}

	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);

		$this->context->controller->addJqueryPlugin('sortable');
		$this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/payment_methods.js');
		$this->context->controller->addCSS($this->module->getPathUri() . 'views/css/admin/payment_methods.css');
	}
}
