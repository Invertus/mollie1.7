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

        /** @var \Mollie\Provider\Config\FormValuesProvider $credentialsFormValuesProvider */
        $credentialsFormValuesProvider = $this->module->getMollieContainer(\Mollie\Provider\Config\CredentialsFormValuesProvider::class);

        $helper->fields_value = $credentialsFormValuesProvider->getFormValues();

        /** @var \Mollie\Builder\Form\CredentialsForm\CredentialsForm $generalSettingsForm */
        $credentialsForm = $this->module->getMollieContainer(\Mollie\Builder\Form\CredentialsForm\CredentialsForm::class);

        $this->content .= $helper->generateForm($credentialsForm->buildParams());
    }

    public function postProcess()
    {
        if (!Tools::isSubmit('submitCredentialsConfiguration')) {
            return parent::postProcess();
        }




//        $errors = [];
//
//        /** @var \Mollie\Service\SettingsSaveService $saveSettingsService */
        $saveSettingsService = $this->module->getMollieContainer(\Mollie\Service\SettingsSaveService::class);
//        $resultMessages = $saveSettingsService->saveSettings($errors);
//        if (!empty($errors)) {
//            $this->context->controller->errors = $resultMessages;
//        } else {
//            $this->context->controller->confirmations = $resultMessages;
//        }

        return parent::postProcess();
    }
}
