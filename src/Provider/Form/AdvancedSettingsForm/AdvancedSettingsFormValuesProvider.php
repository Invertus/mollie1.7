<?php

namespace Mollie\Provider\Form\AdvancedSettingsForm;

use Configuration;
use Mollie\Config\Config;
use Mollie\Provider\Form\FormValuesProvider;
use Tools;

class AdvancedSettingsFormValuesProvider implements FormValuesProvider
{
	public function getFormValues()
	{
		$formValues = [
			Config::MOLLIE_PAYMENTSCREEN_LOCALE => Configuration::get(Config::MOLLIE_PAYMENTSCREEN_LOCALE),
			Config::MOLLIE_KLARNA_INVOICE_ON => Configuration::get(Config::MOLLIE_KLARNA_INVOICE_ON),
		];

		$formValues = array_merge($formValues, $this->getMailSettingsFormValues());
		$formValues = array_merge($formValues, $this->getVisualSettingsFormValues());
		$formValues = array_merge($formValues, $this->getShipmentSettingsFormValues());
		$formValues = array_merge($formValues, $this->getDebugSettingsFormValues());
		$formValues = array_merge($formValues, $this->getOrderStatusesSettingsFormValues());

		return $formValues;
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
}
