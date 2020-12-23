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

use Mollie\Config\Config;

class AdminMollieAdvancedSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
    }

    public function initContent()
    {
        parent::initContent();

        /** @var \Mollie\Repository\ModuleRepository $moduleRepository */
        $moduleRepository = $this->module->getMollieContainer(\Mollie\Repository\ModuleRepository::class);
        $moduleDatabaseVersion = $moduleRepository->getModuleDatabaseVersion($this->module->name);
        if ($moduleDatabaseVersion < $this->module->version) {
            $this->context->controller->errors[] = $this->l('Please upgrade Mollie module.');

            return;
        }

        /** @var \Mollie\Service\Content\TemplateParserInterface $templateParser */
        $templateParser = $this->module->getMollieContainer(\Mollie\Service\Content\TemplateParserInterface::class);

        if (!Configuration::get('PS_SMARTY_FORCE_COMPILE')) {
            $this->context->controller->errors[] = $templateParser->parseTemplate(
                $this->context->smarty,
                $this->module->getMollieContainer(\Mollie\Builder\Content\SmartyForceCompileInfoBlock::class),
                $this->module->getLocalPath() . 'views/templates/hook/smarty_error.tpl'
            );

            $this->context->controller->warnings[] = $templateParser->parseTemplate(
                $this->context->smarty,
                $this->module->getMollieContainer(\Mollie\Builder\Content\SmartyForceCompileInfoBlock::class),
                $this->module->getLocalPath() . 'views/templates/hook/smarty_warning.tpl'
            );
        }

        if (Configuration::get('PS_SMARTY_CACHE') && 'never' === Configuration::get('PS_SMARTY_CLEAR_CACHE')) {
            $this->context->controller->errors[] = $templateParser->parseTemplate(
                $this->context->smarty,
                $this->module->getMollieContainer(\Mollie\Builder\Content\SmartyCacheInfoBlock::class),
                $this->module->getLocalPath() . 'views/templates/hook/smarty_error.tpl'
            );
        }

        if (\Mollie\Utility\CartPriceUtility::checkRoundingMode()) {
            $this->context->controller->errors[] = $templateParser->parseTemplate(
                $this->context->smarty,
                $this->module->getMollieContainer(\Mollie\Builder\Content\RoundingModeInfoBlock::class),
                $this->module->getLocalPath() . 'views/templates/hook/rounding_error.tpl'
            );
        }

        $isSubmitted = (bool) Tools::isSubmit("submit{$this->module->name}");

        /* @phpstan-ignore-next-line */
        if (false === Configuration::get(Mollie\Config\Config::MOLLIE_STATUS_AWAITING) && !$isSubmitted) {
            $this->context->controller->errors[] = $this->l('Please select order status for the "Status for Awaiting payments" field in the "Advanced settings" tab');
        }

        $errors = [];

        if (Tools::isSubmit("submit{$this->module->name}")) {
            /** @var \Mollie\Service\SettingsSaveService $saveSettingsService */
            $saveSettingsService = $this->module->getMollieContainer(\Mollie\Service\SettingsSaveService::class);
            $resultMessages = $saveSettingsService->saveSettings($errors);
            if (!empty($errors)) {
                $this->context->controller->errors = $resultMessages;
            } else {
                $this->context->controller->confirmations = $resultMessages;
            }
        }

        Media::addJsDef([
            'description_message' => $this->l('Description cannot be empty'),
            'profile_id_message' => $this->l('Wrong profile ID'),
            'profile_id_message_empty' => addslashes($this->l('Profile ID cannot be empty')),
            'payment_api' => Mollie\Config\Config::MOLLIE_PAYMENTS_API,
            'ajaxUrl' => $this->context->link->getAdminLink('AdminMollieAjax'),
        ]);

        /* Custom logo JS vars*/
        Media::addJsDef([
            'image_size_message' => $this->l('Image size must be %s%x%s1%'),
            'not_valid_file_message' => $this->l('not a valid file: %s%'),
        ]);

        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/method_countries.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/validation.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/settings.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/custom_logo.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/upgrade_notice.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/api_key_test.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/order_total_restriction_refresh.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin/init_mollie_account.js');
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/mollie.css');
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/admin/logo_input.css');

        $this->content = $templateParser->parseTemplate(
            $this->context->smarty,
            $this->module->getMollieContainer(\Mollie\Builder\Content\LogoInfoBlock::class),
            $this->module->getLocalPath() . 'views/templates/admin/logo.tpl'
        );

        /** @var \Mollie\Builder\Content\UpdateMessageInfoBlock $updateMessageInfoBlock */
        $updateMessageInfoBlock = $this->module->getMollieContainer(\Mollie\Builder\Content\UpdateMessageInfoBlock::class);
        $updateMessageInfoBlockData = $updateMessageInfoBlock->setAddons(Mollie::ADDONS);

        $this->content .= $templateParser->parseTemplate(
            $this->context->smarty,
            $updateMessageInfoBlockData,
            $this->module->getLocalPath() . 'views/templates/admin/updateMessage.tpl'
        );
//
        /** @var \Mollie\Builder\Content\BaseInfoBlock $baseInfoBlock */
        $baseInfoBlock = $this->module->getMollieContainer(\Mollie\Builder\Content\BaseInfoBlock::class);
        $this->context->smarty->assign($baseInfoBlock->buildParams());
//
        /** @var \Mollie\Builder\FormBuilder $settingsFormBuilder */
        $settingsFormBuilder = $this->module->getMollieContainer(\Mollie\Builder\FormBuilder::class);

        try {
            $this->content .= $settingsFormBuilder->buildAdvancedSettingsForm();
        } catch (PrestaShopDatabaseException $e) {
            $this->context->controller->errors[] = $this->l('You are missing database tables. Try resetting module.');
        }

        $this->context->smarty->assign('content', $this->content);
    }

//	public function postProcess()
//	{


//		if (Config::isVersion17()) {
//			Tools::redirectAdmin(
//			/* @phpstan-ignore-next-line */
//			$this->context->link->getAdminLink(
//					'AdminModules',
//					true,
//					[],
//					[
//						'configure' => 'mollie',
//					]
//				)
//			);
//		}

//		Tools::redirectAdmin(
//			$this->context->link->getAdminLink('AdminModules') . '&configure=mollie'
//		);
//	}
}
