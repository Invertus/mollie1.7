<?php

namespace Mollie\Controller;

use Configuration;
use Media;
use ModuleAdminController;
use Mollie;
use Mollie\Builder\Content\LogoInfoBlock;
use Mollie\Builder\Content\RoundingModeInfoBlock;
use Mollie\Builder\Content\SmartyCacheInfoBlock;
use Mollie\Builder\Content\SmartyForceCompileInfoBlock;
use Mollie\Builder\Content\UpdateMessageInfoBlock;
use Mollie\Repository\ModuleRepository;
use Mollie\Service\Content\TemplateParserInterface;
use Mollie\Utility\CartPriceUtility;
use Tab;

class AbstractAdminController extends ModuleAdminController
{
	/** @var Mollie */
	public $module;

	protected function checkModuleErrors()
	{
		/** @var TemplateParserInterface $templateParser */
		$templateParser = $this->module->getMollieContainer(TemplateParserInterface::class);

		if (!Configuration::get('PS_SMARTY_FORCE_COMPILE')) {
			$this->context->controller->warnings[] = $templateParser->parseTemplate(
				$this->context->smarty,
				$this->module->getMollieContainer(SmartyForceCompileInfoBlock::class),
				$this->module->getLocalPath() . 'views/templates/hook/smarty_error.tpl'
			);
		}

		if (Configuration::get('PS_SMARTY_CACHE') && 'never' === Configuration::get('PS_SMARTY_CLEAR_CACHE')) {
			$this->context->controller->errors[] = $templateParser->parseTemplate(
				$this->context->smarty,
				$this->module->getMollieContainer(SmartyCacheInfoBlock::class),
				$this->module->getLocalPath() . 'views/templates/hook/smarty_error.tpl'
			);
		}

		if (CartPriceUtility::checkRoundingMode()) {
			$this->context->controller->errors[] = $templateParser->parseTemplate(
				$this->context->smarty,
				$this->module->getMollieContainer(RoundingModeInfoBlock::class),
				$this->module->getLocalPath() . 'views/templates/hook/rounding_error.tpl'
			);
		}

		/* @phpstan-ignore-next-line */
		if (false === Configuration::get(Mollie\Config\Config::MOLLIE_STATUS_AWAITING)) {
			$this->context->controller->errors[] = $this->module->l('Please select order status for the "Status for Awaiting payments" field in the "Advanced settings" tab');
		}
	}

	protected function setContentValues()
	{
		/** @var TemplateParserInterface $templateParser */
		$templateParser = $this->module->getMollieContainer(TemplateParserInterface::class);

		$this->content = $templateParser->parseTemplate(
			$this->context->smarty,
			$this->module->getMollieContainer(LogoInfoBlock::class),
			$this->module->getLocalPath() . 'views/templates/admin/logo.tpl'
		);

		/** @var UpdateMessageInfoBlock $updateMessageInfoBlock */
		$updateMessageInfoBlock = $this->module->getMollieContainer(UpdateMessageInfoBlock::class);
		$updateMessageInfoBlockData = $updateMessageInfoBlock->setAddons(Mollie::ADDONS);

		$this->content .= $templateParser->parseTemplate(
			$this->context->smarty,
			$updateMessageInfoBlockData,
			$this->module->getLocalPath() . 'views/templates/admin/updateMessage.tpl'
		);
	}

	/** TODO figure out which are needed for which tab and extract */
	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);

		Media::addJsDef([
			'description_message' => $this->module->l('Description cannot be empty'),
			'profile_id_message' => $this->module->l('Wrong profile ID'),
			'profile_id_message_empty' => addslashes($this->module->l('Profile ID cannot be empty')),
			'payment_api' => Mollie\Config\Config::MOLLIE_PAYMENTS_API,
			'ajaxUrl' => $this->context->link->getAdminLink('AdminMollieAjax'),
		]);

		/* Custom logo JS vars*/
		Media::addJsDef([
			'image_size_message' => $this->module->l('Image size must be %s%x%s1%'),
			'not_valid_file_message' => $this->module->l('not a valid file: %s%'),
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
	}

	public function initContent()
	{
	    parent::initContent();
		/** @var ModuleRepository $moduleRepository */
		$moduleRepository = $this->module->getMollieContainer(ModuleRepository::class);
		$moduleDatabaseVersion = $moduleRepository->getModuleDatabaseVersion($this->module->name);
		if ($moduleDatabaseVersion < $this->module->version) {
			$this->context->controller->errors[] = $this->module->l('Please upgrade Mollie module.');

			return;
		}

		$this->checkModuleErrors();
		$this->setContentValues();
	}
}
