<?php

namespace Mollie\Form\Admin\GeneralSettings\Type;

use Attribute;
use Category;
use Configuration;
use Mollie;
use Mollie\Builder\LegacyTranslatorAwareType;
use Mollie\Config\Config;
use Mollie\Form\FormBuilderInterface;
use Mollie\Form\TypeInterface;
use Mollie\Provider\CreditCardLogoProvider;
use Mollie\Service\ApiService;
use Mollie\Service\CountryService;
use Mollie\Utility\TagsUtility;

class LegacyPaymentMethodType extends LegacyTranslatorAwareType implements TypeInterface
{
	/**
	 * @var Mollie
	 */
	private $module;

	/**
	 * @var CreditCardLogoProvider
	 */
	private $creditCardLogoProvider;

	/**
	 * @var CountryService
	 */
	private $countryService;

	/**
	 * @var ApiService
	 */
	private $apiService;

	public function setModule(Mollie $module)
	{
		$this->module = $module;
	}

	public function setApiService(ApiService $apiService)
	{
		$this->apiService = $apiService;
	}

	public function setCountryService(CountryService $countryService)
	{
		$this->countryService = $countryService;
	}

	public function setCreditCardLogoProvider(CreditCardLogoProvider $creditCardLogoProvider)
	{
		$this->creditCardLogoProvider = $creditCardLogoProvider;
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$dateStamp = Mollie\Utility\TimeUtility::getCurrentTimeStamp();
		$molliePaymentMethods = $this->apiService->getMethodsForConfig($this->module->api, $this->module->getPathUri());

		if (empty($molliePaymentMethods)) {
			$builder
				->add('EmptyPaymentMethodAlert', null, [
					'type' => 'mollie-payment-empty-alert',
					'name' => '',
				])
			;
		}

		$builder
			->add(Config::METHODS_CONFIG, null, [
				'type' => 'mollie-methods',
				'name' => Config::METHODS_CONFIG,
				'paymentMethods' => $molliePaymentMethods,
				'countries' => $this->countryService->getActiveCountriesList(),
				'onlyOrderMethods' => array_merge(Config::KLARNA_PAYMENTS, ['voucher']),
				'displayErrors' => Configuration::get(Config::MOLLIE_DISPLAY_ERRORS),
				'methodDescription' => TagsUtility::ppTags(
					$this->module->l('Click [1]here[/1] to read more about the differences between the Payment and Orders API.', 'FormBuilder'),
					[
						$this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_method_info.tpl'),
					]
				),
				'showCustomLogo' => Configuration::get(Config::MOLLIE_SHOW_CUSTOM_LOGO),
				'customLogoUrl' => $this->creditCardLogoProvider->getLogoPathUri() . "?{$dateStamp}",
				'customLogoExist' => $this->creditCardLogoProvider->logoExists(),
				'voucherCategory' => Configuration::get(Config::MOLLIE_VOUCHER_CATEGORY),
				'categoryList' => Category::getCategories($this->module->getContext()->language->id, true, false),
				'productAttributes' => Attribute::getAttributes($this->module->getContext()->language->id),
				'klarnaPayments' => Config::KLARNA_PAYMENTS,
				'klarnaStatuses' => [Config::MOLLIE_STATUS_KLARNA_AUTHORIZED, Config::MOLLIE_STATUS_KLARNA_SHIPPED],
			])
		;
	}
}
