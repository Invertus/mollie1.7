<?php

namespace Mollie\Service\Form\AdvancedSettingsForm;

use Carrier;
use Configuration;
use Context;
use Mollie\Adapter\ToolsAdapter;
use Mollie\Config\Config;
use Mollie\Service\Form\FormSaver;
use Mollie\Service\MolCarrierInformationService;
use MolliePrefix\Mollie\Api\Types\PaymentStatus;
use OrderState;
use Tools;

class AdvancedSettingsFormSaver implements FormSaver
{
    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    /**
     * @var MolCarrierInformationService
     */
    private $carrierInformationService;

    public function __construct(
        ToolsAdapter $toolsAdapter,
        MolCarrierInformationService $carrierInformationService
    ) {
        $this->toolsAdapter = $toolsAdapter;
        $this->carrierInformationService = $carrierInformationService;
    }

    public function saveConfiguration()
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_PAYMENTSCREEN_LOCALE, $this->toolsAdapter->getValue(Config::MOLLIE_PAYMENTSCREEN_LOCALE));
        $success &= $this->saveMailSettingsFormValues();
        $success &= $this->saveVisualSettingsFormValues();
        $success &= $this->saveShipmentSettingsFormValues();
        $success &= $this->saveDebugSettingsFormValues();
        $success &= $this->saveOrderStatusesSettings();
        $this->handleKlarnaInvoiceStatus();

        return (bool) $success;
    }

    /**
     * @return bool
     */
    private function saveMailSettingsFormValues()
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_SEND_ORDER_CONFIRMATION, $this->toolsAdapter->getValue(Config::MOLLIE_SEND_ORDER_CONFIRMATION));
        $success &= Configuration::updateValue(Config::MOLLIE_SEND_NEW_ORDER, $this->toolsAdapter->getValue(Config::MOLLIE_SEND_NEW_ORDER));

        return (bool) $success;
    }

    /**
     * @return bool
     */
    private function saveVisualSettingsFormValues()
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_IMAGES, $this->toolsAdapter->getValue(Config::MOLLIE_IMAGES));
        $success &= Configuration::updateValue(Config::MOLLIE_CSS, $this->toolsAdapter->getValue(Config::MOLLIE_CSS));

        return (bool) $success;
    }

    /**
     * @return bool
     */
    private function saveShipmentSettingsFormValues()
    {
        $success = true;

        $success &= Configuration::updateValue(
            Config::MOLLIE_TRACKING_URLS,
            json_encode(@json_decode($this->toolsAdapter->getValue(Config::MOLLIE_TRACKING_URLS)))
        );

        $success &= Configuration::updateValue(
            Config::MOLLIE_AUTO_SHIP_STATUSES,
            json_encode($this->getStatusesValue(Config::MOLLIE_AUTO_SHIP_STATUSES))
        );

        $success &= Configuration::updateValue(Config::MOLLIE_AUTO_SHIP_MAIN, $this->toolsAdapter->getValue(Config::MOLLIE_AUTO_SHIP_MAIN));

        $carriers = Carrier::getCarriers(
            Context::getContext()->language->id,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );

        foreach ($carriers as $carrier) {
            $success &= $this->carrierInformationService->saveMolCarrierInfo(
                $carrier['id_carrier'],
                $this->toolsAdapter->getValue(Config::MOLLIE_CARRIER_URL_SOURCE . $carrier['id_carrier']),
                $this->toolsAdapter->getValue(Config::MOLLIE_CARRIER_CUSTOM_URL . $carrier['id_carrier'])
            );
        }

        return (bool) $success;
    }

    /**
     * @return bool
     */
    private function saveDebugSettingsFormValues()
    {
        $success = true;

        $success &= Configuration::updateValue(Config::MOLLIE_DISPLAY_ERRORS, $this->toolsAdapter->getValue(Config::MOLLIE_DISPLAY_ERRORS));
        $success &= Configuration::updateValue(Config::MOLLIE_DEBUG_LOG, $this->toolsAdapter->getValue(Config::MOLLIE_DEBUG_LOG));

        return (bool) $success;
    }

    /**
     * @return bool
     */
    private function saveOrderStatusesSettings()
    {
        $success = true;

        foreach (array_keys(Config::getStatuses()) as $name) {
            $name = Tools::strtoupper($name);
            if (false === Tools::getValue("MOLLIE_STATUS_{$name}")) {
                continue;
            }
            $new = (int) Tools::getValue("MOLLIE_STATUS_{$name}");
            $success &= Configuration::updateValue("MOLLIE_STATUS_{$name}", $new);
            Config::getStatuses()[Tools::strtolower($name)] = $new;

            if (PaymentStatus::STATUS_OPEN != $name) {
                $success &= Configuration::updateValue(
                    "MOLLIE_MAIL_WHEN_{$name}",
                    Tools::getValue("MOLLIE_MAIL_WHEN_{$name}") ? true : false
                );
            }
        }
        return (bool) $success;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    private function getStatusesValue($key)
    {
        $statesEnabled = [];
        $context = Context::getContext();
        foreach (OrderState::getOrderStates($context->language->id) as $state) {
            if (Tools::isSubmit($key . '_' . $state['id_order_state'])) {
                $statesEnabled[] = $state['id_order_state'];
            }
        }

        return $statesEnabled;
    }

    private function handleKlarnaInvoiceStatus()
    {
        $klarnaInvoiceStatus = Tools::getValue(Config::MOLLIE_KLARNA_INVOICE_ON);
        Configuration::updateValue(Config::MOLLIE_KLARNA_INVOICE_ON, $klarnaInvoiceStatus);
        if (Config::MOLLIE_STATUS_KLARNA_SHIPPED === $klarnaInvoiceStatus) {
            $this->updateKlarnaStatuses(true);

            return;
        }

        $this->updateKlarnaStatuses(false);
    }

    private function updateKlarnaStatuses($isShipped = true)
    {
        $klarnaInvoiceShippedId = Configuration::get(Config::MOLLIE_STATUS_KLARNA_SHIPPED);
        $klarnaInvoiceShipped = new OrderState((int) $klarnaInvoiceShippedId);
        $klarnaInvoiceShipped->invoice = $isShipped;
        $klarnaInvoiceShipped->update();

        $klarnaInvoiceAcceptedId = Configuration::get(Config::MOLLIE_STATUS_KLARNA_AUTHORIZED);
        $klarnaInvoiceAccepted = new OrderState((int) $klarnaInvoiceAcceptedId);

        $klarnaInvoiceAccepted->invoice = !$isShipped;
        $klarnaInvoiceAccepted->update();
    }
}