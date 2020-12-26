<?php

namespace Mollie\Service\Form\CredentialsForm;

use Configuration;
use Mollie\Adapter\ToolsAdapter;
use Mollie\Config\Config;
use Mollie\Service\Form\FormSaver;

class CredentialsFormSaver implements FormSaver
{
	/**
	 * @var ToolsAdapter
	 */
	private $toolsAdapter;

	public function __construct(ToolsAdapter $toolsAdapter)
	{
		$this->toolsAdapter = $toolsAdapter;
	}

	/**
	 * @return bool
	 */
	public function saveConfiguration()
	{
		$success = true;

		$success &= $this->saveApiSettings();

		return (bool) $success;
	}

	/**
	 * @return bool
	 */
	private function saveApiSettings()
	{
		$success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_ACCOUNT_SWITCH, $this->toolsAdapter->getValue(Config::MOLLIE_ACCOUNT_SWITCH));
        $success &= Configuration::updateValue(Config::MOLLIE_API_KEY_TEST, $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY_TEST));
        $success &= Configuration::updateValue(Config::MOLLIE_API_KEY, $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY));
		$success &= Configuration::updateValue(Config::MOLLIE_PROFILE_ID, $this->toolsAdapter->getValue(Config::MOLLIE_PROFILE_ID));
		$success &= Configuration::updateValue(Config::MOLLIE_ENVIRONMENT, $this->toolsAdapter->getValue(Config::MOLLIE_ENVIRONMENT));

		return (bool) $success;
	}
}
