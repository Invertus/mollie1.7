<?php

namespace Mollie\Form\Admin\Credentials;

use Configuration;
use HelperForm;
use Mollie;
use Mollie\Form\Admin\Credentials\FormBuilder\LegacyCredentialsFormBuilder;
use Tools;

class LegacyCredentialsForm implements CredentialsFormInterface
{
	/**
	 * @var array
	 */
	private $data = [];

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
	public function isSubmitted()
	{
		return Tools::isSubmit('submit' . LegacyCredentialsFormBuilder::FORM_NAME . 'Configuration');
	}

	/**
	 * {@inheritDoc}
	 */
	public function isValid()
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setData($value)
	{
		$this->data = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createView()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->module->getTable();
		$helper->module = $this->module;
		$helper->default_form_language = $this->module->getContext()->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->module->getIdentifier();
		$helper->submit_action = 'submit' . LegacyCredentialsFormBuilder::FORM_NAME . 'Configuration';
		$helper->token = Tools::getAdminTokenLite(Mollie::ADMIN_MOLLIE_CREDENTIALS_CONTROLLER);

		/** @var CredentialsFormDataProvider $credentialsFormDataProvider */
		$credentialsFormDataProvider = $this->module->getMollieContainer(CredentialsFormDataProvider::class);
		$helper->fields_value = $credentialsFormDataProvider->getData();

		return $helper->generateForm([
			[
				'form' => [
					'input' => $this->getData(),
					'submit' => [
						'title' => $this->module->l('Save'),
						'class' => 'btn btn-default pull-right',
					],
				],
			],
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function handleRequest($request)
	{
		return $this;
	}
}
