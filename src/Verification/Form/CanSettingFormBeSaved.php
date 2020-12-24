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
            throw new FormSettingVerificationException(
                'Failed to save settings: credentials are incorrect',
                FormSettingVerificationException::INCORRECT_CREDENTIALS
            );
        }

        return true;
    }

    //TODO fix this
    private function areCredentialsCorrect()
    {
        $mollieApiKey = $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY);
        $mollieApiKeyTest = $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY_TEST);
        $environment = (int) $this->toolsAdapter->getValue(Config::MOLLIE_ENVIRONMENT);

        //TODO providers for this to check if is correct.
        if (empty($this->toolsAdapter->getValue('submitCredentialsConfiguration'))) {
            $mollieApiKey = $this->configurationAdapter->get(Config::MOLLIE_API_KEY);
            $mollieApiKeyTest = $this->configurationAdapter->get(Config::MOLLIE_API_KEY_TEST);

            if (strpos($mollieApiKey, 'live') !== 0) {
                return true;
            }

            if (strpos($mollieApiKeyTest, 'test') !== 0) {
                return true;
            }
        }

        $apiKey = Config::ENVIRONMENT_LIVE === (int) $environment ? $mollieApiKey : $mollieApiKeyTest;
        $isApiKeyIncorrect = 0 !== strpos($apiKey, 'live') && 0 !== strpos($apiKey, 'test');

        return !$isApiKeyIncorrect;
    }
}