<?php

namespace Mollie\Builder\Form\CredentialsForm;

use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\EnvironmentUtility;
use Mollie\Utility\TagsUtility;

class CredentialsForm implements TemplateBuilderInterface
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
			'type' => 'mollie-support',
			'name' => '',
		];

		if (!EnvironmentUtility::getApiKey()) {
			$input[] = [
				'type' => 'mollie-switch',
				'label' => $this->module->l('Do you already have a Mollie account?', 'FormBuilder'),
				'name' => Config::MOLLIE_ACCOUNT_SWITCH,
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
				'desc' => $this->module->display(
					$this->module->getPathUri(), 'views/templates/admin/create_new_account_link.tpl'
				),
			];
		}

		$input[] = [
			'type' => 'select',
			'label' => $this->module->l('Environment', 'FormBuilder'),
			'name' => Config::MOLLIE_ENVIRONMENT,
			'options' => [
				'query' => [
					[
						'id' => Config::ENVIRONMENT_TEST,
						'name' => $this->module->l('Test', 'FormBuilder'),
					],
					[
						'id' => Config::ENVIRONMENT_LIVE,
						'name' => $this->module->l('Live', 'FormBuilder'),
					],
				],
				'id' => 'id',
				'name' => 'name',
			],
		];

		$input[] = [
			'type' => 'mollie-password',
			'label' => $this->module->l('API Key Test', 'FormBuilder'),
			'desc' => TagsUtility::ppTags(
				$this->module->l('You can find your API key in your [1]Mollie Profile[/1]; it starts with test or live.', 'FormBuilder'),
				[$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
			),
			'name' => Config::MOLLIE_API_KEY_TEST,
			'required' => true,
			'class' => 'fixed-width-xxl',
			'form_group_class' => 'js-test-api-group',
		];

		$input[] = [
			'type' => 'mollie-password',
			'label' => $this->module->l('API Key Live', 'FormBuilder'),
			'name' => Config::MOLLIE_API_KEY,
			'required' => true,
			'class' => 'fixed-width-xxl',
			'form_group_class' => 'js-live-api-group',
		];

		$input[] = [
			'type' => 'mollie-password',
			'label' => $this->module->l('Profile ID', 'FormBuilder'),
			'desc' => TagsUtility::ppTags(
				$this->module->l('You can find your Profile ID in your [1]Mollie Profile[/1]', 'FormBuilder'),
				[$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
			),
			'name' => Config::MOLLIE_PROFILE_ID,
			'required' => true,
			'class' => 'fixed-width-xxl',
			'form_group_class' => 'js-api-profile-id',
		];

		if (EnvironmentUtility::getApiKey()) {
			$input[] = [
				'type' => 'mollie-button',
				'label' => '',
				'name' => Config::MOLLIE_API_KEY_TESTING_BUTTON,
				'text' => $this->module->l('Test ApiKey', 'FormBuilder'),
				'class' => 'js-test-api-keys',
				'form_group_class' => 'js-api-key-test',
			];
		}

		$input[] = [
			'type' => 'mollie-h3',
			'name' => '',
			'title' => '',
		];

		return $input;
	}
}
