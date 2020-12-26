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

        foreach ($data as $key => $configuration) {
            if (Tools::getValue($key, null) !== null) {
                $success &= Configuration::updateValue($key, Tools::getValue($key));
            }
        }

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
}