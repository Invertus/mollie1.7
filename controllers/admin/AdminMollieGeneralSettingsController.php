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
use Mollie\Builder\Form\GeneralSettingsForm\GeneralSettingsForm;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Exception\PaymentMethodConfigurationUpdaterException;
use Mollie\Provider\Form\FormValuesProvider;
use Mollie\Provider\Form\GeneralSettingsForm\GeneralSettingsFormValuesProvider;
use Mollie\Service\ExceptionService;
use Mollie\Service\Form\FormSaver;
use Mollie\Service\Form\GeneralSettingsForm\GeneralSettingsFormSaver;
use Mollie\Verification\Form\CanSettingFormBeSaved;
use Mollie\Verification\Form\FormSettingVerification;

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
        $helper->submit_action = 'submitGeneralSettingsConfiguration';
        $helper->token = Tools::getAdminTokenLite(Mollie::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER);

        /** @var BaseInfoBlock $baseInfoBlock */
        $baseInfoBlock = $this->module->getMollieContainer(BaseInfoBlock::class);
        $this->context->smarty->assign($baseInfoBlock->buildParams());

        /** @var FormValuesProvider $generalSettingsFormValuesProvider */
        $generalSettingsFormValuesProvider = $this->module->getMollieContainer(GeneralSettingsFormValuesProvider::class);

        $helper->fields_value = $generalSettingsFormValuesProvider->getFormValues();

        /** @var TemplateBuilderInterface $generalSettingsForm */
        $generalSettingsForm = $this->module->getMollieContainer(GeneralSettingsForm::class);

        $this->content .= $helper->generateForm($generalSettingsForm->buildParams());
    }

    public function postProcess()
    {
        if (!Tools::isSubmit('submitGeneralSettingsConfiguration')) {
            return parent::postProcess();
        }

        try {
            /** @var FormSettingVerification $canSettingFormBeSaved */
            $canSettingFormBeSaved = $this->module->getMollieContainer(CanSettingFormBeSaved::class);

            if ($canSettingFormBeSaved->verify()) {
                /** @var GeneralSettingsFormSaver $generalSettingsFormSaver */
                $generalSettingsFormSaver = $this->module->getMollieContainer(GeneralSettingsFormSaver::class);
                $generalSettingsFormSaver->saveConfiguration();
            }
        } catch (FormSettingVerificationException $exception) {
            /** @var ExceptionService $exceptionService */
            $exceptionService = $this->module->getMollieContainer(ExceptionService::class);
            $this->errors[] = $exceptionService->getErrorMessageForException(
                $exception,
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
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->context->controller->addJqueryPlugin('sortable');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/payment_methods.js');
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/admin/payment_methods.css');
    }
}
