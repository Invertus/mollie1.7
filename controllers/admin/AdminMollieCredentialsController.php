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

use Mollie\Builder\Form\CredentialsForm\CredentialsFormInterface;
use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Service\ExceptionService;
use Mollie\Service\Form\CredentialsForm\CredentialsFormSaver;
use Mollie\Verification\Form\CanSettingFormBeSaved;
use Mollie\Verification\Form\FormSettingVerification;

class AdminMollieCredentialsController extends AbstractAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		parent::__construct();
	}

	public function initContent()
	{
		parent::initContent();

		/** @var CredentialsFormInterface $credentialsForm */
		$credentialsForm = $this->module->getMollieContainer(CredentialsFormInterface::class);
		$this->content = $credentialsForm->parse();

		$this->context->smarty->assign('content', $this->content);
	}

	public function postProcess()
	{
		if (!Tools::isSubmit('submitCredentialsConfiguration')) {
			return parent::postProcess();
		}

		try {
			/** @var FormSettingVerification $canSettingFormBeSaved */
			$canSettingFormBeSaved = $this->module->getMollieContainer(CanSettingFormBeSaved::class);

			if ($canSettingFormBeSaved->verify()) {
				/** @var CredentialsFormSaver $credentialsFormSaver */
				$credentialsFormSaver = $this->module->getMollieContainer(CredentialsFormSaver::class);
				$credentialsFormSaver->saveConfiguration();
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
	}
}
