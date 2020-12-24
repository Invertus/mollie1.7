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

use Mollie\Builder\Form\CredentialsForm\CredentialsForm;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Provider\Form\CredentialsFormValuesProvider;
use Mollie\Provider\Form\FormValuesProvider;
use Mollie\Service\ExceptionService;
use Mollie\Service\Form\CredentialsForm\CredentialsFormSaver;
use Mollie\Service\Form\FormSaver;
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
        $helper->submit_action = 'submitCredentialsConfiguration';
        $helper->token = Tools::getAdminTokenLite(Mollie::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER);

        /** @var FormValuesProvider $credentialsFormValuesProvider */
        $credentialsFormValuesProvider = $this->module->getMollieContainer(CredentialsFormValuesProvider::class);

        $helper->fields_value = $credentialsFormValuesProvider->getFormValues();

        /** @var TemplateBuilderInterface $credentialsForm */
        $credentialsForm = $this->module->getMollieContainer(CredentialsForm::class);

        $this->content .= $helper->generateForm($credentialsForm->buildParams());
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
                /** @var FormSaver $credentialsFormSaver */
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
