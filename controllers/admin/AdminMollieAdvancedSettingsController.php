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

use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Form\Admin\AdvancedSettings\AdvancedSettingsFormDataProvider;
use Mollie\Form\Admin\AdvancedSettings\AdvancedSettingsFormInterface;
use Mollie\Form\Admin\AdvancedSettings\FormBuilder\AdvancedSettingsFormBuilderInterface;
use Mollie\Service\ExceptionService;
use Mollie\Verification\Form\CanSettingFormBeSaved;
use Mollie\Verification\Form\FormSettingVerification;

class AdminMollieAdvancedSettingsController extends AbstractAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		parent::__construct();
	}

	public function initContent()
	{
		parent::initContent();

		/** @var AdvancedSettingsFormBuilderInterface $advancedSettingsFormBuilder */
		$advancedSettingsFormBuilder = $this->module->getMollieContainer(AdvancedSettingsFormBuilderInterface::class);

		$this->content .= $advancedSettingsFormBuilder->getForm()->createView();

		$this->context->smarty->assign([
			'content' => $this->content,
		]);
	}

	public function postProcess()
	{
		/** @var AdvancedSettingsFormBuilderInterface $advancedSettingsFormBuilder */
		$advancedSettingsFormBuilder = $this->module->getMollieContainer(AdvancedSettingsFormBuilderInterface::class);

		/** @var AdvancedSettingsFormInterface $advancedSettingsForm */
		$advancedSettingsForm = $advancedSettingsFormBuilder->getForm();

		$advancedSettingsForm->handleRequest(null);

		if ($advancedSettingsForm->isSubmitted() && $advancedSettingsForm->isValid()) {
			try {
				/** @var FormSettingVerification $canSettingFormBeSaved */
				$canSettingFormBeSaved = $this->module->getMollieContainer(CanSettingFormBeSaved::class);

				if ($canSettingFormBeSaved->verify()) {
					/** @var AdvancedSettingsFormDataProvider $advancedSettingsFormDataProvider */
					$advancedSettingsFormDataProvider = $this->module->getMollieContainer(AdvancedSettingsFormDataProvider::class);
					$advancedSettingsFormDataProvider->setData($advancedSettingsFormDataProvider->getData());
				}
			} catch (FormSettingVerificationException $e) {
				/** @var ExceptionService $exceptionService */
				$exceptionService = $this->module->getMollieContainer(ExceptionService::class);
				$this->errors[] = $exceptionService->getErrorMessageForException(
					$e,
					$exceptionService->getErrorMessages()
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
