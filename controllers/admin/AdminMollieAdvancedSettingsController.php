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

		/** @var \Mollie\Repository\ModuleRepository $moduleRepository */
		$moduleRepository = $this->module->getMollieContainer(\Mollie\Repository\ModuleRepository::class);
		$moduleDatabaseVersion = $moduleRepository->getModuleDatabaseVersion($this->module->name);
		if ($moduleDatabaseVersion < $this->module->version) {
			$this->context->controller->errors[] = $this->l('Please upgrade Mollie module.');

			return;
		}

		$this->checkModuleErrors();
		$this->setContentValues();

		$this->renderAdvancedSettingsForm();

		$this->context->smarty->assign('content', $this->content);
	}

	public function renderAdvancedSettingsForm()
	{
		/** @var \Mollie\Builder\Content\BaseInfoBlock $baseInfoBlock */
		$baseInfoBlock = $this->module->getMollieContainer(\Mollie\Builder\Content\BaseInfoBlock::class);
		$this->context->smarty->assign($baseInfoBlock->buildParams());

		/** @var \Mollie\Builder\FormBuilder $settingsFormBuilder */
		$settingsFormBuilder = $this->module->getMollieContainer(\Mollie\Builder\FormBuilder::class);

		try {
			$this->content .= $settingsFormBuilder->buildAdvancedSettingsForm();
		} catch (PrestaShopDatabaseException $e) {
			$this->context->controller->errors[] = $this->module->l('You are missing database tables. Try resetting module.');
		}
	}

	public function postProcess()
	{
		if (!Tools::isSubmit('submitAdvancedSettings')) {
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
}
