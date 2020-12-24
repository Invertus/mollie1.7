<?php

namespace Mollie\Service\Form\GeneralSettingsForm;

use Configuration;
use Mollie\Adapter\ToolsAdapter;
use Mollie\Config\Config;
use Mollie\Service\Form\FormSaver;

class GeneralSettingsFormSaver implements FormSaver
{
    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    public function __construct(ToolsAdapter $toolsAdapter)
    {
        $this->toolsAdapter = $toolsAdapter;
    }

    public function saveConfiguration()
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_IFRAME, $this->toolsAdapter->getValue(Config::MOLLIE_IFRAME));
        $success &= Configuration::updateValue(Config::MOLLIE_SINGLE_CLICK_PAYMENT, $this->toolsAdapter->getValue(Config::MOLLIE_SINGLE_CLICK_PAYMENT));
        $success &= Configuration::updateValue(Config::MOLLIE_ISSUERS, $this->toolsAdapter->getValue(Config::MOLLIE_ISSUERS));

        if ($this->toolsAdapter->getValue(Config::METHODS_CONFIG) && json_decode($this->toolsAdapter->getValue(Config::METHODS_CONFIG))) {
            $success &= Configuration::updateValue(
                Config::METHODS_CONFIG,
                json_encode(@json_decode($this->toolsAdapter->getValue(Config::METHODS_CONFIG)))
            );
        }

        return $success;
    }
}