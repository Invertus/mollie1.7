<?php

namespace Mollie\Verification\Form;

use Mollie;
use Mollie\Adapter\ConfigurationAdapter;
use Mollie\Adapter\ToolsAdapter;
use Mollie\Config\Config;
use Mollie\Exception\FormSettingVerificationException;

class CanSettingFormBeSaved implements FormSettingVerification
{
	/**
	 * @var ConfigurationAdapter
	 */
	private $configurationAdapter;

	/**
	 * @var ToolsAdapter
	 */
	private $toolsAdapter;

	/**
	 * @var Mollie
	 */
	private $module;

	public function __construct(Mollie $module, ConfigurationAdapter $configurationAdapter, ToolsAdapter $toolsAdapter)
	{
		$this->configurationAdapter = $configurationAdapter;
		$this->toolsAdapter = $toolsAdapter;
		$this->module = $module;
	}

	public function verify()
	{
		if (!$this->areCredentialsCorrect()) {
			throw new FormSettingVerificationException('Failed to save settings: credentials are incorrect', FormSettingVerificationException::INCORRECT_CREDENTIALS);
		}

		return true;
	}

	//TODO fix this
	private function areCredentialsCorrect()
	{
		$mollieApiKey = $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY, $this->configurationAdapter->get(Config::MOLLIE_API_KEY));
		$mollieApiKeyTest = $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY_TEST, $this->configurationAdapter->get(Config::MOLLIE_API_KEY_TEST));
		$environment = (int) $this->toolsAdapter->getValue(Config::MOLLIE_ENVIRONMENT, $this->configurationAdapter->get(Config::MOLLIE_ENVIRONMENT));

		if ($environment === Config::ENVIRONMENT_LIVE) {
			return strpos($mollieApiKey, 'live') === 0;
		} else {
			return strpos($mollieApiKeyTest, 'test') === 0;
		}
	}
}
