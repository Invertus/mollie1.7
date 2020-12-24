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

use Mollie\Builder\Form\AdvancedSettingsForm\AdvancedSettingsForm;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Provider\Form\AdvancedSettingsForm\AdvancedSettingsFormValuesProvider;
use Mollie\Provider\Form\FormValuesProvider;
use Mollie\Service\ExceptionService;
use Mollie\Service\Form\AdvancedSettingsForm\AdvancedSettingsFormSaver;
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

		$this->renderForm();

		$this->context->smarty->assign('content', $this->content);
	}

	public function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->module->getTable();
		$helper->module = $this->module;
		$helper->default_form_language = $this->module->getContext()->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->module->getIdentifier();
		$helper->submit_action = 'submitAdvancedSettingsConfiguration';
		$helper->token = Tools::getAdminTokenLite(Mollie::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER);

		/** @var FormValuesProvider $advancedSettingsFormValuesProvider */
		$advancedSettingsFormValuesProvider = $this->module->getMollieContainer(AdvancedSettingsFormValuesProvider::class);

		$helper->fields_value = $advancedSettingsFormValuesProvider->getFormValues();

		/** @var TemplateBuilderInterface $credentialsForm */
		$credentialsForm = $this->module->getMollieContainer(AdvancedSettingsForm::class);

		$this->content .= $helper->generateForm($credentialsForm->buildParams());
	}

	public function postProcess()
	{
		if (!Tools::isSubmit('submitAdvancedSettingsConfiguration')) {
			return parent::postProcess();
		}

		try {
			/** @var FormSettingVerification $canSettingFormBeSaved */
			$canSettingFormBeSaved = $this->module->getMollieContainer(CanSettingFormBeSaved::class);

			if ($canSettingFormBeSaved->verify()) {
				/** @var AdvancedSettingsFormSaver $credentialsFormSaver */
				$credentialsFormSaver = $this->module->getMollieContainer(AdvancedSettingsFormSaver::class);
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
