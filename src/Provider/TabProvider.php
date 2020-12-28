<?php

namespace Mollie\Provider;

use Mollie;

class TabProvider
{
	/**
	 * @var Mollie
	 */
	private $module;

	public function __construct(Mollie $module)
	{
		$this->module = $module;
	}

	/**
	 * @return array
	 */
	public function getModuleTabs()
	{
		return [
			[
				'name' => $this->module->l('Mollie parent'),
				'parent_class_name' => 'AdminParentModulesSf',
				'class_name' => Mollie::ADMIN_MOLLIE_PARENT_CONTROLLER,
				'visible' => false,
				'active' => false,
			],
			[
				'name' => $this->module->l('Credentials'),
				'parent_class_name' => Mollie::ADMIN_MOLLIE_PARENT_CONTROLLER,
				'class_name' => Mollie::ADMIN_MOLLIE_CREDENTIALS_CONTROLLER,
			],
			[
				'name' => $this->module->l('General Settings'),
				'parent_class_name' => Mollie::ADMIN_MOLLIE_PARENT_CONTROLLER,
				'class_name' => Mollie::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER,
			],
			[
				'name' => $this->module->l('Advanced Settings'),
				'parent_class_name' => Mollie::ADMIN_MOLLIE_PARENT_CONTROLLER,
				'class_name' => Mollie::ADMIN_MOLLIE_ADVANCED_SETTINGS_CONTROLLER,
			],
		];
	}

	/**
	 * @return array
	 */
	public function getSidebarTabs()
	{
		return [
			[
				'name' => $this->module->l('Mollie'),
				'parent_class_name' => 'improve',
				'class_name' => Mollie::ADMIN_MOLLIE_MODULE_CONTROLLER,
				'parent' => 'improve',
				'active' => true,
				'icon' => 'mollie',
			],
		];
	}
}
