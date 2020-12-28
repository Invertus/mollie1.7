<?php

namespace Mollie\Form\Admin\GeneralSettings\Type;

use Mollie;
use Mollie\Builder\LegacyTranslatorAwareType;
use Mollie\Config\Config;
use Mollie\Form\FormBuilderInterface;
use Mollie\Form\TypeInterface;
use Mollie\Utility\TagsUtility;

class LegacyGeneralSettingsType extends LegacyTranslatorAwareType implements TypeInterface
{
	/**
	 * @var Mollie
	 */
	private $module;

	public function setModule(Mollie $module)
	{
		$this->module = $module;
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('Information-top', null, [
				'type' => 'mollie-support',
				'name' => '',
			])
			->add(Config::MOLLIE_IFRAME, null, [
				'type' => 'switch',
				'label' => $this->module->l('Use Mollie Components for CreditCards', 'FormBuilder'),
				'name' => Config::MOLLIE_IFRAME,
				'desc' => TagsUtility::ppTags(
					$this->module->l('Read more about [1]Mollie Components[/1] and how it improves your conversion.', 'FormBuilder'),
					[$this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_components_info.tpl')]
				),
				$this->module->l('Read more about Mollie Components and how it improves your conversion', 'FormBuilder'),
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
			])
			->add(Config::MOLLIE_SINGLE_CLICK_PAYMENT, null, [
				'type' => 'switch',
				'label' => $this->module->l('Use Single Click Payments for CreditCards', 'FormBuilder'),
				'name' => Config::MOLLIE_SINGLE_CLICK_PAYMENT,
				'desc' => TagsUtility::ppTags(
					$this->module->l('Read more about [1]Single Click Payments[/1] and how it improves your conversion.', 'FormBuilder'),
					[
						$this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_single_click_payment_info.tpl'),
					]
				),
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
			])
			->add(Config::MOLLIE_ISSUERS, null, [
				'type' => 'select',
				'label' => $this->module->l('Issuer list', 'FormBuilder'),
				'desc' => $this->module->l('Some payment methods (eg. iDEAL) have an issuer list. This setting specifies where it is shown.', 'FormBuilder'),
				'name' => Config::MOLLIE_ISSUERS,
				'options' => [
					'query' => [
						[
							'id' => Config::ISSUERS_ON_CLICK,
							'name' => $this->module->l('On click', 'FormBuilder'),
						],
						[
							'id' => Config::ISSUERS_PAYMENT_PAGE,
							'name' => $this->module->l('Payment page', 'FormBuilder'),
						],
					],
					'id' => 'id',
					'name' => 'name',
				],
			])
			->add(Config::MOLLIE_BUTTON_ORDER_TOTAL_REFRESH, null, [
				'type' => 'mollie-button-update-order-total-restriction',
				'label' => '',
				'name' => Config::MOLLIE_BUTTON_ORDER_TOTAL_REFRESH,
				'text' => $this->module->l('Refresh order total restriction values', 'FormBuilder'),
				'class' => 'js-refresh-order-total-values',
				'form_group_class' => 'js-refresh-order-total',
				'help' => $this->module->l('Will refresh all available payment method order total restriction values by all currencies', 'FormBuilder'),
			])
			->add('InformationBottom', null, [
				'type' => 'mollie-h2',
				'name' => '',
				'title' => $this->module->l('Payment methods', 'FormBuilder'),
			])
			->add('PaymentMethods', LegacyPaymentMethodType::class, [])
		;
	}
}
