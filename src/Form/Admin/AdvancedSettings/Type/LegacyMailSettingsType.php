<?php

namespace Mollie\Form\Admin\AdvancedSettings\Type;

use Module;
use Mollie;
use Mollie\Builder\LegacyTranslatorAwareType;
use Mollie\Config\Config;
use Mollie\Form\FormBuilderInterface;
use Mollie\Form\TypeInterface;
use Mollie\Utility\TagsUtility;

class LegacyMailSettingsType extends LegacyTranslatorAwareType implements TypeInterface
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
			->add(Config::MOLLIE_SEND_ORDER_CONFIRMATION, null, [
				'type' => 'select',
				'label' => $this->module->l('Send order confirmation email', 'FormBuilder'),
				'name' => Config::MOLLIE_SEND_ORDER_CONFIRMATION,
				'options' => [
					'query' => [
						[
							'id' => Config::ORDER_CONF_MAIL_SEND_ON_CREATION,
							'name' => $this->module->l('When Order is created', 'FormBuilder'),
						],
						[
							'id' => Config::ORDER_CONF_MAIL_SEND_ON_PAID,
							'name' => $this->module->l('When Order is Paid', 'FormBuilder'),
						],
						[
							'id' => Config::ORDER_CONF_MAIL_SEND_ON_NEVER,
							'name' => $this->module->l('Never', 'FormBuilder'),
						],
					],
					'id' => 'id',
					'name' => 'name',
				],
			])
		;

		if (Module::isEnabled(Config::EMAIL_ALERTS_MODULE_NAME)) {
			$builder
				->add(Config::MOLLIE_SEND_NEW_ORDER, null, [
					'type' => 'select',
					'label' => $this->module->l('Send new order email to merchant', 'FormBuilder'),
					'name' => Config::MOLLIE_SEND_NEW_ORDER,
					'desc' => TagsUtility::ppTags(
						$this->module->l('Change when \'new_order\' email to merchant is sent (When using PrestaShop Mail Alerts module)', 'FormBuilder'),
						[$this->module->display($this->module->getPathUri(), 'views/templates/admin/locale_wiki.tpl')]
					),
					'options' => [
						'query' => [
							[
								'id' => Config::NEW_ORDER_MAIL_SEND_ON_CREATION,
								'name' => $this->module->l('When Order is created', 'FormBuilder'),
							],
							[
								'id' => Config::NEW_ORDER_MAIL_SEND_ON_PAID,
								'name' => $this->module->l('When Order is Paid', 'FormBuilder'),
							],
							[
								'id' => Config::NEW_ORDER_MAIL_SEND_ON_NEVER,
								'name' => $this->module->l('Never', 'FormBuilder'),
							],
						],
						'id' => 'id',
						'name' => 'name',
					],
				])
			;
		}
	}
}
