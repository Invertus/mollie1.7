<?php

namespace Mollie\Provider\Form\CredentialsForm;

use Configuration;
use Mollie\Config\Config;
use Mollie\Provider\Form\FormValuesProvider;

class CredentialsFormValuesProvider implements FormValuesProvider
{
    public function getFormValues()
    {
        $formValues = [
            Config::MOLLIE_ACCOUNT_SWITCH => Configuration::get(Config::MOLLIE_ACCOUNT_SWITCH),
        ];

        $formValues = array_merge($formValues, $this->getApiSettings());

        return $formValues;
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