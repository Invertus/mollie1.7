<?php

namespace Mollie\Builder\Form\AdvancedSettingsForm;

use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\TagsUtility;

class VisualSettingsBlock implements TemplateBuilderInterface
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
	 * {@inheritDoc}
	 */
	public function buildParams()
	{
		$input = [];

		$input[] = [
			'type' => 'mollie-h2',
			'name' => '',
			'title' => $this->module->l('Visual Settings', 'FormBuilder'),
		];

		$input[] = [
			'type' => 'select',
			'label' => $this->module->l('Images', 'FormBuilder'),
			'desc' => $this->module->l('Show big, normal or no payment method logos on checkout.', 'FormBuilder'),
			'name' => Config::MOLLIE_IMAGES,
			'options' => [
				'query' => [
					[
						'id' => Config::LOGOS_HIDE,
						'name' => $this->module->l('hide', 'FormBuilder'),
					],
					[
						'id' => Config::LOGOS_NORMAL,
						'name' => $this->module->l('normal', 'FormBuilder'),
					],
					[
						'id' => Config::LOGOS_BIG,
						'name' => $this->module->l('big', 'FormBuilder'),
					],
				],
				'id' => 'id',
				'name' => 'name',
			],
		];

		$input[] = [
			'type' => 'text',
			'label' => $this->module->l('CSS file', 'FormBuilder'),
			'desc' => TagsUtility::ppTags(
				$this->module->l('Leave empty for default stylesheet. Should include file path when set. Hint: You can use [1]{BASE}[/1], [1]{THEME}[/1], [1]{CSS}[/1], [1]{MOBILE}[/1], [1]{MOBILE_CSS}[/1] and [1]{OVERRIDE}[/1] for easy folder mapping.', 'FormBuilder'),
				[$this->module->display($this->module->getPathUri(), 'views/templates/front/kbd.tpl')]
			),
			'name' => Config::MOLLIE_CSS,
			'class' => 'long-text',
		];

		return $input;
	}
}
