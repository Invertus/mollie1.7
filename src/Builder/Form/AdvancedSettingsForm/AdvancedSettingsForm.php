<?php

namespace Mollie\Builder\Form\AdvancedSettingsForm;

use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\TagsUtility;

class AdvancedSettingsForm implements TemplateBuilderInterface
{
	/**
	 * @var Mollie
	 */
	private $module;

	/**
	 * @var OrderStatusesSettingsBlock
	 */
	private $orderStatusesBlock;

	/**
	 * @var ShipmentSettingsBlock
	 */
	private $shipmentSettingsBlock;

	/**
	 * @var MailSettingsBlock
	 */
	private $mailSettingsBlock;

	/**
	 * @var VisualSettingsBlock
	 */
	private $visualSettingsBlock;

	/**
	 * @var DebugSettingsBlock
	 */
	private $debugSettingsBlock;

	public function __construct(
		Mollie $module,
		OrderStatusesSettingsBlock $orderStatusesBlock,
		MailSettingsBlock $mailSettingsBlock,
		ShipmentSettingsBlock $shipmentSettingsBlock,
		VisualSettingsBlock $visualSettingsBlock,
		DebugSettingsBlock $debugSettingsBlock
	) {
		$this->module = $module;
		$this->orderStatusesBlock = $orderStatusesBlock;
		$this->shipmentSettingsBlock = $shipmentSettingsBlock;
		$this->mailSettingsBlock = $mailSettingsBlock;
		$this->visualSettingsBlock = $visualSettingsBlock;
		$this->debugSettingsBlock = $debugSettingsBlock;
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildParams()
	{
		return [
			[
				'form' => [
					'input' => $this->getFormInputs(),
					'submit' => [
						'title' => $this->module->l('Save'),
						'class' => 'btn btn-default pull-right',
					],
				],
			],
		];
	}

	private function getFormInputs()
	{
		$input = [];

		$input[] = [
			'type' => 'select',
			'label' => $this->module->l('Push Locale to Payment Screen', 'FormBuilder'),
			'desc' => TagsUtility::ppTags(
				$this->module->l('When activated, Mollie will use your webshop\'s [1]Locale[/1]. If not, the browser\'s locale will be used.', 'FormBuilder'),
				[$this->module->display($this->module->getPathUri(), 'views/templates/admin/locale_wiki.tpl')]
			),
			'name' => Config::MOLLIE_PAYMENTSCREEN_LOCALE,
			'options' => [
				'query' => [
					[
						'id' => Config::PAYMENTSCREEN_LOCALE_SEND_WEBSITE_LOCALE,
						'name' => $this->module->l('Yes, push webshop Locale', 'FormBuilder'),
					],
					[
						'id' => Config::PAYMENTSCREEN_LOCALE_BROWSER_LOCALE,
						'name' => $this->module->l('No, use browser\'s Locale', 'FormBuilder'),
					],
				],
				'id' => 'id',
				'name' => 'name',
			],
		];

		if (Config::isVersion17()) {
			$this->mailSettingsBlock->buildParams();
		}

		$input[] = [
			'type' => 'select',
			'label' => $this->module->l('When to create the invoice?', 'FormBuilder'),
			'desc' => $this->module->display($this->module->getPathUri(), 'views/templates/admin/invoice_description.tpl'),
			'name' => Config::MOLLIE_KLARNA_INVOICE_ON,
			'options' => [
				'query' => [
					[
						'id' => Config::MOLLIE_STATUS_KLARNA_AUTHORIZED,
						'name' => $this->module->l('Accepted', 'FormBuilder'),
					],
					[
						'id' => Config::MOLLIE_STATUS_KLARNA_SHIPPED,
						'name' => $this->module->l('Shipped', 'FormBuilder'),
					],
				],
				'id' => 'id',
				'name' => 'name',
			],
		];

		$input = array_merge($input, $this->orderStatusesBlock->buildParams());
		$input = array_merge($input, $this->visualSettingsBlock->buildParams());
		$input = array_merge($input, $this->shipmentSettingsBlock->buildParams());
		$input = array_merge($input, $this->debugSettingsBlock->buildParams());

		return $input;
	}
}
