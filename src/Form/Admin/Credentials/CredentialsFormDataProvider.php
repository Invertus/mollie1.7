<?php

namespace Mollie\Form\Admin\Credentials;

use Configuration;
use Mollie\Config\Config;
use Mollie\Form\FormDataProviderInterface;
use Tools;

class CredentialsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getData()
    {
        $configurationData = [
            Config::MOLLIE_ACCOUNT_SWITCH => Configuration::get(Config::MOLLIE_ACCOUNT_SWITCH),
        ];

        $configurationData = array_merge($configurationData, $this->getApiSettings());

        return $configurationData;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_ACCOUNT_SWITCH, Tools::getValue(Config::MOLLIE_ACCOUNT_SWITCH));
        $success &= $this->saveApiSettings();

        return (bool) $success;
    }

    /**
     * @return array
     */
    private function getApiSettings()
    {
        return [
            Config::MOLLIE_ENVIRONMENT => Configuration::get(Config::MOLLIE_ENVIRONMENT),
            Config::MOLLIE_API_KEY_TEST => Configuration::get(Config::MOLLIE_API_KEY_TEST),
            Config::MOLLIE_API_KEY => Configuration::get(Config::MOLLIE_API_KEY),
            Config::MOLLIE_PROFILE_ID => Configuration::get(Config::MOLLIE_PROFILE_ID),
        ];
    }

    /**
     * @return bool
     */
    private function saveApiSettings()
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_ENVIRONMENT, Tools::getValue(Config::MOLLIE_ENVIRONMENT));
        $success &= Configuration::updateValue(Config::MOLLIE_API_KEY_TEST, Tools::getValue(Config::MOLLIE_API_KEY_TEST));
        $success &= Configuration::updateValue(Config::MOLLIE_API_KEY, Tools::getValue(Config::MOLLIE_API_KEY));
        $success &= Configuration::updateValue(Config::MOLLIE_PROFILE_ID, Tools::getValue(Config::MOLLIE_PROFILE_ID));

        return (bool) $success;
    }
}