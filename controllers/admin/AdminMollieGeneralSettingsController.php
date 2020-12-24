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

class AdminMollieGeneralSettingsController extends AbstractAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $this->renderSettingsForm();

        $this->context->smarty->assign('content', $this->content);
    }

    public function renderSettingsForm()
    {
        /** @var \Mollie\Builder\Content\BaseInfoBlock $baseInfoBlock */
        $baseInfoBlock = $this->module->getMollieContainer(\Mollie\Builder\Content\BaseInfoBlock::class);
        $this->context->smarty->assign($baseInfoBlock->buildParams());

        /** @var \Mollie\Builder\Form\GeneralSettingsForm\GeneralSettingsFormSaver $generalSettingsForm */
        $generalSettingsForm = $this->module->getMollieContainer(\Mollie\Builder\Form\GeneralSettingsForm\GeneralSettingsFormSaver::class);

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->module->getTable();
        $helper->module = $this->module;
        $helper->default_form_language = $this->module->getContext()->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->module->getIdentifier();
        $helper->submit_action = 'submitGeneralSettingsConfiguration';
        $helper->token = Tools::getAdminTokenLite(Mollie::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER);

        /** @var \Mollie\Service\ConfigFieldService $configFieldService */
        $configFieldService = $this->module->getMollieContainer(\Mollie\Service\ConfigFieldService::class);

        $helper->tpl_vars = [
            'fields_value' =>  $configFieldService->getConfigFieldsValues(),
            'languages' => $this->module->getContext()->controller->getLanguages(),
            'id_language' => $this->module->getContext()->language->id,
        ];

        $this->content .= $helper->generateForm($generalSettingsForm->buildParams());
    }

    public function postProcess()
    {
        if (!Tools::isSubmit('submitGeneralSettingsConfiguration')) {
            return parent::postProcess();
        }

        $errors = [];

        /** @var \Mollie\Service\SettingsSaveService $saveSettingsService */
        $saveSettingsService = $this->module->getMollieContainer(\Mollie\Service\SettingsSaveService::class);
        $resultMessages = $saveSettingsService->saveSettings($errors);
        if (!empty($errors)) {
            $this->context->controller->errors = $resultMessages;
        } else {
            $this->context->controller->confirmations = $resultMessages;
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
