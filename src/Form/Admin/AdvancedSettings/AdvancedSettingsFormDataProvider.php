<?php

namespace Mollie\Form\Admin\AdvancedSettings;

use Carrier;
use Configuration;
use Context;
use Mollie\Config\Config;
use Mollie\Form\FormDataProviderInterface;
use Mollie\Service\MolCarrierInformationService;
use MolliePrefix\Mollie\Api\Types\PaymentStatus;
use OrderState;
use Tools;

class AdvancedSettingsFormDataProvider implements FormDataProviderInterface
{
	/**
	 * @var MolCarrierInformationService
	 */
	private $carrierInformationService;

	public function __construct(MolCarrierInformationService $carrierInformationService)
	{
		$this->carrierInformationService = $carrierInformationService;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getData()
	{
		$configurationData = [
			Config::MOLLIE_PAYMENTSCREEN_LOCALE => Configuration::get(Config::MOLLIE_PAYMENTSCREEN_LOCALE),
			Config::MOLLIE_KLARNA_INVOICE_ON => Configuration::get(Config::MOLLIE_KLARNA_INVOICE_ON),
		];

		$configurationData = array_merge($configurationData, $this->getMailSettingsFormValues());
		$configurationData = array_merge($configurationData, $this->getVisualSettingsFormValues());
		$configurationData = array_merge($configurationData, $this->getShipmentSettingsFormValues());
		$configurationData = array_merge($configurationData, $this->getDebugSettingsFormValues());
		$configurationData = array_merge($configurationData, $this->getOrderStatusesSettingsFormValues());

		return $configurationData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setData(array $data)
	{
		$success = true;

		$success &= Configuration::updateValue(Config::MOLLIE_PAYMENTSCREEN_LOCALE, Tools::getValue(Config::MOLLIE_PAYMENTSCREEN_LOCALE));
		$success &= $this->saveMailSettingsFormValues();
		$success &= $this->saveVisualSettingsFormValues();
		$success &= $this->saveShipmentSettingsFormValues();
		$success &= $this->saveDebugSettingsFormValues();
		$success &= $this->saveOrderStatusesSettings();
		$this->handleKlarnaInvoiceStatus();

		return (bool) $success;
	}

	/**
	 * @return array
	 */
	private function getMailSettingsFormValues()
	{
		return [
			Config::MOLLIE_SEND_ORDER_CONFIRMATION => Configuration::get(Config::MOLLIE_SEND_ORDER_CONFIRMATION),
			Config::MOLLIE_SEND_NEW_ORDER => Configuration::get(Config::MOLLIE_SEND_NEW_ORDER),
		];
	}

	/**
	 * @return array
	 */
	private function getVisualSettingsFormValues()
	{
		return [
			Config::MOLLIE_IMAGES => Configuration::get(Config::MOLLIE_IMAGES),
			Config::MOLLIE_CSS => Configuration::get(Config::MOLLIE_CSS),
		];
	}

	/**
	 * @return array
	 */
	private function getShipmentSettingsFormValues()
	{
		$formValues = [
			Config::MOLLIE_TRACKING_URLS => Configuration::get(Config::MOLLIE_TRACKING_URLS),
			Config::MOLLIE_AUTO_SHIP_MAIN => Configuration::get(Config::MOLLIE_AUTO_SHIP_MAIN),
		];

		$checkStatuses = [];

		if (Configuration::get(Config::MOLLIE_AUTO_SHIP_STATUSES)) {
			$automaticShipmentOrderStatuses = @json_decode(Configuration::get(Config::MOLLIE_AUTO_SHIP_STATUSES), true);
		}
		if (!isset($automaticShipmentOrderStatuses) || !is_array($automaticShipmentOrderStatuses)) {
			$automaticShipmentOrderStatuses = [];
		}

		foreach ($automaticShipmentOrderStatuses as $automaticShipmentOrderStatus) {
			$checkStatuses[Config::MOLLIE_AUTO_SHIP_STATUSES . '_' . (int) $automaticShipmentOrderStatus] = true;
		}

		$formValues = array_merge($formValues, $checkStatuses);

		return $formValues;
	}

	/**
	 * @return array
	 */
	private function getDebugSettingsFormValues()
	{
		return [
			Config::MOLLIE_DISPLAY_ERRORS => Configuration::get(Config::MOLLIE_DISPLAY_ERRORS),
			Config::MOLLIE_DEBUG_LOG => Configuration::get(Config::MOLLIE_DEBUG_LOG),
		];
	}

	/**
	 * @return array
	 */
	private function getOrderStatusesSettingsFormValues()
	{
		$formValues = [];

		foreach (array_keys(Config::getStatuses()) as $name) {
			$name = Tools::strtoupper($name);
			$formValues['MOLLIE_STATUS_' . $name] = Configuration::get('MOLLIE_STATUS_' . $name);
			$formValues['MOLLIE_MAIL_WHEN_' . $name] = Configuration::get('MOLLIE_MAIL_WHEN_' . $name);
		}

		return $formValues;
	}

	/**
	 * @return bool
	 */
	private function saveMailSettingsFormValues()
	{
		$success = true;

		$success &= Configuration::updateValue(Config::MOLLIE_SEND_ORDER_CONFIRMATION, Tools::getValue(Config::MOLLIE_SEND_ORDER_CONFIRMATION));
		$success &= Configuration::updateValue(Config::MOLLIE_SEND_NEW_ORDER, Tools::getValue(Config::MOLLIE_SEND_NEW_ORDER));

		return (bool) $success;
	}

	/**
	 * @return bool
	 */
	private function saveVisualSettingsFormValues()
	{
		$success = true;

		$success &= Configuration::updateValue(Config::MOLLIE_IMAGES, Tools::getValue(Config::MOLLIE_IMAGES));
		$success &= Configuration::updateValue(Config::MOLLIE_CSS, Tools::getValue(Config::MOLLIE_CSS));

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
			json_encode(@json_decode(Tools::getValue(Config::MOLLIE_TRACKING_URLS)))
		);

		$success &= Configuration::updateValue(
			Config::MOLLIE_AUTO_SHIP_STATUSES,
			json_encode($this->getStatusesValue(Config::MOLLIE_AUTO_SHIP_STATUSES))
		);

		$success &= Configuration::updateValue(Config::MOLLIE_AUTO_SHIP_MAIN, Tools::getValue(Config::MOLLIE_AUTO_SHIP_MAIN));

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
				Tools::getValue(Config::MOLLIE_CARRIER_URL_SOURCE . $carrier['id_carrier']),
				Tools::getValue(Config::MOLLIE_CARRIER_CUSTOM_URL . $carrier['id_carrier'])
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

		$success &= Configuration::updateValue(Config::MOLLIE_DISPLAY_ERRORS, Tools::getValue(Config::MOLLIE_DISPLAY_ERRORS));
		$success &= Configuration::updateValue(Config::MOLLIE_DEBUG_LOG, Tools::getValue(Config::MOLLIE_DEBUG_LOG));

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
