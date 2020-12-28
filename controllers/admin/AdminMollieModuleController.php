<?php

use Mollie\Utility\EnvironmentUtility;

class AdminMollieModuleController extends ModuleAdminController
{
    public function postProcess()
    {
        if (EnvironmentUtility::getApiKey()) {
            Tools::redirectAdmin($this->context->link->getAdminLink(Mollie::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER));
        }

        Tools::redirectAdmin($this->context->link->getAdminLink(Mollie::ADMIN_MOLLIE_CREDENTIALS_CONTROLLER));
        return parent::postProcess();
    }
}