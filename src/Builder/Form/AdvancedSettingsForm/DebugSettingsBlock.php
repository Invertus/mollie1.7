<?php

namespace Mollie\Builder\Form\AdvancedSettingsForm;

use Mollie;
use Mollie\Adapter\LegacyContext;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\TagsUtility;

class DebugSettingsBlock implements TemplateBuilderInterface
{
	/**
	 * @var Mollie
	 */
	private $module;

	/**
	 * @var LegacyContext
	 */
	private $context;

	public function __construct(Mollie $module, LegacyContext $context)
	{
		$this->module = $module;
		$this->context = $context;
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildParams()
	{
		$input = [];

		$this->context->getSmarty()->assign([
			'logs' => $this->context->getLink()->getAdminLink('AdminLogs'),
		]);

		$input[] = [
			'type' => 'mollie-h2',
			'name' => '',
			'title' => $this->module->l('Debug level', 'FormBuilder'),
		];

		$input[] = [
			'type' => 'switch',
			'label' => $this->module->l('Display errors', 'FormBuilder'),
			'name' => Config::MOLLIE_DISPLAY_ERRORS,
			'desc' => $this->module->l('Enabling this feature will display error messages (if any) on the front page. Use for debug purposes only!', 'FormBuilder'),
			'is_bool' => true,
			'values' => [
				[
					'id' => 'active_on',
					'value' => true,
					'label' => $this->module->l('Enabled', 'FormBuilder'),
				],
				[
					'id' => 'active_off',
					'value' => false,
					'label' => $this->module->l('Disabled', 'FormBuilder'),
				],
			],
		];

		$input[] = [
			'type' => 'select',
			'label' => $this->module->l('Log level', 'FormBuilder'),
			'desc' => TagsUtility::ppTags(
				$this->module->l('Recommended level: Errors. Set to Everything to monitor incoming webhook requests. [1]View logs.[/1]', 'FormBuilder'),
				[
					$this->module->display($this->module->getPathUri(), 'views/templates/admin/view_logs.tpl'),
				]
			),
			'name' => Config::MOLLIE_DEBUG_LOG,
			'options' => [
				'query' => [
					[
						'id' => Config::DEBUG_LOG_NONE,
						'name' => $this->module->l('Nothing', 'FormBuilder'),
					],
					[
						'id' => Config::DEBUG_LOG_ERRORS,
						'name' => $this->module->l('Errors', 'FormBuilder'),
					],
					[
						'id' => Config::DEBUG_LOG_ALL,
						'name' => $this->module->l('Everything', 'FormBuilder'),
					],
				],
				'id' => 'id',
				'name' => 'name',
			],
		];

		return $input;
	}
}
