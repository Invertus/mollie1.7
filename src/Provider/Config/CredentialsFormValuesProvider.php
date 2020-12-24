<?php

namespace Mollie\Provider\Config;

use Configuration;
use Mollie\Config\Config;

class CredentialsFormValuesProvider implements FormValuesProvider
{
    public function getFormValues()
    {
        return [
            Config::MOLLIE_ACCOUNT_SWITCH => Configuration::get(Config::MOLLIE_ACCOUNT_SWITCH),
            Config::MOLLIE_ENVIRONMENT => Configuration::get(Config::MOLLIE_ENVIRONMENT),
            Config::MOLLIE_API_KEY_TEST => Configuration::get(Config::MOLLIE_API_KEY_TEST),
            Config::MOLLIE_API_KEY => Configuration::get(Config::MOLLIE_API_KEY),
        ];
    }
}