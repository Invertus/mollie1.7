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

    //TODO add setting updated from mollie to add form savers.

    /**
     * @return bool
     */
    public function saveConfiguration()
    {
        $success = true;

        $hasAccount = $this->toolsAdapter->getValue(Config::MOLLIE_ACCOUNT_SWITCH);

        if ($hasAccount) {
            $success &= Configuration::updateValue(Config::MOLLIE_ACCOUNT_SWITCH, $hasAccount);
            $success &= Configuration::updateValue(Config::MOLLIE_API_KEY_TEST, $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY_TEST));
            $success &= Configuration::updateValue(Config::MOLLIE_API_KEY, $this->toolsAdapter->getValue(Config::MOLLIE_API_KEY));
        } else {
            $success &= Configuration::updateValue(Config::MOLLIE_API_KEY_TEST, null);
            $success &= Configuration::updateValue(Config::MOLLIE_API_KEY, null);
        }

        $success &= Configuration::updateValue(Config::MOLLIE_ENVIRONMENT, $this->toolsAdapter->getValue(Config::MOLLIE_ENVIRONMENT));


        return (bool) $success;
    }
}