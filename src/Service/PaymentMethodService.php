<?php
/**
 * Mollie       https://www.mollie.nl
 *
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 *
 * @see        https://github.com/mollie/PrestaShop
 *
 * @license     https://github.com/mollie/PrestaShop/blob/master/LICENSE.md
 * @codingStandardsIgnoreStart
 */

namespace Mollie\Service;

use Address;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Mollie;
use Mollie\Config\Config;
use Mollie\DTO\Object\Amount;
use Mollie\DTO\OrderData;
use Mollie\DTO\PaymentData;
use Mollie\Provider\CreditCardLogoProvider;
use Mollie\Provider\PhoneNumberProviderInterface;
use Mollie\Repository\MethodCountryRepository;
use Mollie\Repository\PaymentMethodRepository;
use Mollie\Service\PaymentMethod\PaymentMethodSortProviderInterface;
use Mollie\Utility\CustomLogoUtility;
use Mollie\Utility\EnvironmentUtility;
use Mollie\Utility\LocaleUtility;
use Mollie\Utility\NumberUtility;
use Mollie\Utility\PaymentFeeUtility;
use Mollie\Utility\TextFormatUtility;
use Mollie\Utility\TextGeneratorUtility;
use MolliePrefix\Mollie\Api\Types\PaymentMethod;
use MolPaymentMethod;
use Order;
use PrestaShopDatabaseException;
use PrestaShopException;
use Tools;

class PaymentMethodService
{
	/**
	 * @var Mollie
	 */
	private $module;

	/**
	 * @var PaymentMethodRepository
	 */
	private $methodRepository;

	/**
	 * @var MethodCountryRepository
	 */
	private $methodCountryRepository;

	/**
	 * @var CartLinesService
	 */
	private $cartLinesService;

	/**
	 * @var PaymentsTranslationService
	 */
	private $paymentsTranslationService;

	/**
	 * @var CustomerService
	 */
	private $customerService;

	/**
	 * @var CreditCardLogoProvider
	 */
	private $creditCardLogoProvider;

	private $paymentMethodSortProvider;

	private $countryRepository;

	private $phoneNumberProvider;

	public function __construct(
		Mollie $module,
		PaymentMethodRepository $methodRepository,
		MethodCountryRepository $methodCountryRepository,
		CartLinesService $cartLinesService,
		PaymentsTranslationService $paymentsTranslationService,
		CustomerService $customerService,
		CreditCardLogoProvider $creditCardLogoProvider,
		PaymentMethodSortProviderInterface $paymentMethodSortProvider,
		PhoneNumberProviderInterface $phoneNumberProvider
	) {
		$this->module = $module;
		$this->methodRepository = $methodRepository;
		$this->methodCountryRepository = $methodCountryRepository;
		$this->cartLinesService = $cartLinesService;
		$this->paymentsTranslationService = $paymentsTranslationService;
		$this->customerService = $customerService;
		$this->creditCardLogoProvider = $creditCardLogoProvider;
		$this->paymentMethodSortProvider = $paymentMethodSortProvider;
		$this->phoneNumberProvider = $phoneNumberProvider;
	}

	public function savePaymentMethod($method)
	{
		$environment = Tools::getValue(Mollie\Config\Config::MOLLIE_ENVIRONMENT);
		$paymentId = $this->methodRepository->getPaymentMethodIdByMethodId($method['id'], $environment);
		$paymentMethod = new MolPaymentMethod();
		if ($paymentId) {
			$paymentMethod = new MolPaymentMethod((int) $paymentId);
		}
		$paymentMethod->id_method = $method['id'];
		$paymentMethod->method_name = $method['name'];
		$paymentMethod->enabled = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_ENABLED . $method['id']);
		$paymentMethod->title = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_TITLE . $method['id']);
		$paymentMethod->method = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_API . $method['id']);
		$paymentMethod->description = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_DESCRIPTION . $method['id']);
		$paymentMethod->is_countries_applicable = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_APPLICABLE_COUNTRIES . $method['id']);
		$paymentMethod->minimal_order_value = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_MINIMUM_ORDER_VALUE . $method['id']);
		$paymentMethod->max_order_value = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_MAX_ORDER_VALUE . $method['id']);
		$paymentMethod->surcharge = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_SURCHARGE_TYPE . $method['id']);
		$paymentMethod->surcharge_fixed_amount = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_SURCHARGE_FIXED_AMOUNT . $method['id']);
		$paymentMethod->surcharge_percentage = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_SURCHARGE_PERCENTAGE . $method['id']);
		$paymentMethod->surcharge_limit = Tools::getValue(Mollie\Config\Config::MOLLIE_METHOD_SURCHARGE_LIMIT . $method['id']);
		$paymentMethod->images_json = json_encode($method['image']);
		$paymentMethod->live_environment = $environment;

		$paymentMethod->save();

		return $paymentMethod;
	}

	/**
	 * Get payment methods to show on the checkout.
	 *
	 * @return array
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 *
	 * @since 3.0.0
	 * @since 3.4.0 public
	 *
	 * @public ✓ This method is part of the public API
	 */
	public function getMethodsForCheckout()
	{
		$apiKey = EnvironmentUtility::getApiKey();
		if (!$apiKey) {
			return [];
		}
		/* @phpstan-ignore-next-line */
		if (false === Configuration::get(Config::MOLLIE_STATUS_AWAITING)) {
			return [];
		}
		$context = Context::getContext();
		$iso = Tools::strtolower($context->currency->iso_code);
		$apiEnvironment = Configuration::get(Config::MOLLIE_ENVIRONMENT);
		$methods = $this->methodRepository->getMethodsForCheckout($apiEnvironment);
		if (empty($methods)) {
			$methods = [];
		}
		$countryCode = Tools::strtolower($context->country->iso_code);
		$unavailableMethods = [];

		foreach (Mollie\Config\Config::$defaultMethodAvailability as $methodName => $countries) {
			if (!in_array($methodName, ['klarnapaylater', 'klarnasliceit'])
				|| empty($countries)
			) {
				continue;
			}
			if (!in_array($countryCode, $countries)) {
				$unavailableMethods[] = $methodName;
			}
		}

		foreach ($methods as $index => $method) {
			$methodObj = new MolPaymentMethod($method['id_payment_method']);
			if (!isset(Mollie\Config\Config::$methodCurrencies[$methodObj->id_method])
				|| !in_array($iso, Mollie\Config\Config::$methodCurrencies[$methodObj->id_method])
				|| !$methodObj->enabled
				|| in_array($methodObj->id_method, $unavailableMethods)
			) {
				unset($methods[$index]);
			}
			if (Mollie\Config\Config::APPLEPAY === $methodObj->id_method) {
				if (!Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
					unset($methods[$index]);
				} elseif ('0' === $_COOKIE['isApplePayMethod']) {
					unset($methods[$index]);
				}
			}
			if (Config::MOLLIE_VOUCHER_METHOD_ID === $methodObj->id_method) {
				$totalOrderCost = $context->cart->getOrderTotal(true);
				if (!$this->isVoucherPaymentAvailable($totalOrderCost)) {
					unset($methods[$index]);
				}
			}
		}

		if (version_compare(_PS_VERSION_, '1.6.0.9', '>')) {
			foreach ($methods as $index => $methodId) {
				$methodObj = new MolPaymentMethod($methodId['id_payment_method']);
				if ($methodObj->is_countries_applicable) {
					if (!$this->methodCountryRepository->checkIfMethodIsAvailableInCountry($methodObj->id_method, $country = Country::getByIso($countryCode))) {
						unset($methods[$index]);
					}
				} else {
					if ($this->methodCountryRepository->checkIfCountryIsExcluded($methodObj->id_method, $country = Country::getByIso($countryCode))) {
						unset($methods[$index]);
					}
				}
			}
		}

		$methods = $this->paymentsTranslationService->getTranslatedPaymentMethods($methods);

		foreach ($methods as $key => $method) {
			$image = json_decode($method['images_json'], true);
			$methods[$key]['image'] = $image;
			if (CustomLogoUtility::isCustomLogoEnabled($method['id_method'])) {
				if ($this->creditCardLogoProvider->logoExists()) {
					$methods[$key]['image']['custom_logo'] = $this->creditCardLogoProvider->getLogoPathUri();
				}
			}
		}

		$methods = $this->paymentMethodSortProvider->getSortedInAscendingWayForCheckout($methods);

		return $methods;
	}

	/**
	 * Get payment data.
	 *
	 * @param float|string $amount
	 * @param string $currency
	 * @param string $method
	 * @param string|null $issuer
	 * @param int|Cart $cartId
	 * @param string $secureKey
	 * @param MolPaymentMethod $molPaymentMethod
	 * @param bool $qrCode
	 * @param string $orderReference
	 * @param string $cardToken
	 *
	 * @return PaymentData|OrderData
	 *
	 * @since 3.3.0 Order reference
	 */
	public function getPaymentData(
		$amount,
		$currency,
		$method,
		$issuer,
		$cartId,
		$secureKey,
		MolPaymentMethod $molPaymentMethod,
		$qrCode = false,
		$orderReference = '',
		$cardToken = ''
	) {
		$totalAmount = TextFormatUtility::formatNumber($amount, 2);
		if (!$orderReference) {
			$this->module->currentOrderReference = $orderReference = Order::generateReference();
		}
		$description = TextGeneratorUtility::generateDescriptionFromCart($molPaymentMethod->description, $cartId, $orderReference);
		$context = Context::getContext();
		$cart = new Cart($cartId);
		$customer = new Customer($cart->id_customer);

		$paymentFee = PaymentFeeUtility::getPaymentFee($molPaymentMethod, $totalAmount);
		$totalAmount += $paymentFee;

		$currency = (string) ($currency ? Tools::strtoupper($currency) : 'EUR');
		$value = (float) TextFormatUtility::formatNumber($totalAmount, 2);
		$amountObj = new Amount($currency, $value);

		$redirectUrl = ($qrCode
			? $context->link->getModuleLink(
				'mollie',
				'qrcode',
				['cart_id' => $cartId, 'done' => 1, 'rand' => time()],
				true
			)
			: $context->link->getModuleLink(
				'mollie',
				'return',
				[
					'cart_id' => $cartId,
					'utm_nooverride' => 1,
					'rand' => time(),
					'key' => $secureKey,
					'customerId' => $customer->id,
				],
				true
			)
		);

		$webhookUrl = null;
		if (!EnvironmentUtility::isLocalEnvironment()) {
			$webhookUrl = $context->link->getModuleLink(
				'mollie',
				'webhook',
				[],
				true
			);
		}

		$metaData = [
			'cart_id' => $cartId,
			'order_reference' => $orderReference,
			'secure_key' => Tools::encrypt($secureKey),
		];

		if (Mollie\Config\Config::MOLLIE_ORDERS_API !== $molPaymentMethod->method) {
			$paymentData = new PaymentData($amountObj, $description, $redirectUrl, $webhookUrl);

			$paymentData->setMetadata($metaData);
			$paymentData->setLocale($this->getLocale($molPaymentMethod->method));
			$paymentData->setMethod($molPaymentMethod->id_method);

			$description = str_ireplace(
				['%'],
				[$cartId],
				$description
			);
			$paymentData->setDescription($description);
			$paymentData->setIssuer($issuer);

			if (isset($cart->id_address_invoice)) {
				$billing = new Address((int) $cart->id_address_invoice);
				$paymentData->setBillingAddress($billing);
			}
			if (isset($cart->id_address_delivery)) {
				$shipping = new Address((int) $cart->id_address_delivery);
				$paymentData->setShippingAddress($shipping);
			}

			if ($cardToken) {
				$paymentData->setCardToken($cardToken);
			}

			if (PaymentMethod::BANKTRANSFER === $method) {
				$paymentData->setLocale(LocaleUtility::getWebShopLocale());
			}

			$isCreditCardPayment = PaymentMethod::CREDITCARD === $molPaymentMethod->id_method;
			if ($isCreditCardPayment && $this->isCustomerSaveEnabled()) {
				$apiCustomer = $this->customerService->processCustomerCreation($cart, $molPaymentMethod->id_method);
				$paymentData->setCustomerId($apiCustomer->id);
			}

			return $paymentData;
		}

		if (Mollie\Config\Config::MOLLIE_ORDERS_API === $molPaymentMethod->method) {
			$orderData = new OrderData($amountObj, $redirectUrl, $webhookUrl);

			if (isset($cart->id_address_invoice)) {
				$billing = new Address((int) $cart->id_address_invoice);

				$orderData->setBillingAddress($billing);
				$orderData->setBillingPhoneNumber($this->phoneNumberProvider->getFromAddress($billing));
			}
			if (isset($cart->id_address_delivery)) {
				$shipping = new Address((int) $cart->id_address_delivery);
				$orderData->setShippingAddress($shipping);
				$orderData->setDeliveryPhoneNumber($this->phoneNumberProvider->getFromAddress($shipping));
			}
			$orderData->setOrderNumber($orderReference);
			$orderData->setLocale($this->getLocale($molPaymentMethod->method));
			$orderData->setEmail($customer->email);
			$orderData->setMethod($molPaymentMethod->id_method);
			$orderData->setMetadata($metaData);

			$currency = new Currency($cart->id_currency);
			$selectedVoucherCategory = Configuration::get(Config::MOLLIE_VOUCHER_CATEGORY);
			$orderData->setLines(
				$this->cartLinesService->getCartLines(
					$amount,
					$paymentFee,
					$currency->iso_code,
					$cart->getSummaryDetails(),
					$cart->getTotalShippingCost(null, true),
					$cart->getProducts(),
					(bool) Configuration::get('PS_GIFT_WRAPPING'),
					$selectedVoucherCategory
				));
			$payment = [];
			if ($cardToken) {
				$payment['cardToken'] = $cardToken;
			}
			if (!EnvironmentUtility::isLocalEnvironment()) {
				$payment['webhookUrl'] = $context->link->getModuleLink(
					'mollie',
					'webhook',
					[],
					true
				);
			}
			if ($issuer) {
				$payment['issuer'] = $issuer;
			}

			$isCreditCardPayment = PaymentMethod::CREDITCARD === $molPaymentMethod->id_method;
			if ($isCreditCardPayment && $this->isCustomerSaveEnabled()) {
				$apiCustomer = $this->customerService->processCustomerCreation($cart, $molPaymentMethod->id_method);
				$payment['customerId'] = $apiCustomer->id;
			}

			$orderData->setPayment($payment);

			return $orderData;
		}
	}

	private function getLocale($method)
	{
		// Send webshop locale
		if ((Mollie\Config\Config::MOLLIE_PAYMENTS_API === $method
				&& Mollie\Config\Config::PAYMENTSCREEN_LOCALE_SEND_WEBSITE_LOCALE === Configuration::get(Mollie\Config\Config::MOLLIE_PAYMENTSCREEN_LOCALE))
			|| Mollie\Config\Config::MOLLIE_ORDERS_API === $method
		) {
			$locale = LocaleUtility::getWebShopLocale();
			if (preg_match(
				'/^[a-z]{2}(?:[\-_][A-Z]{2})?$/iu',
				$locale
			)) {
				return $locale;
			}
		}
	}

	private function isCustomerSaveEnabled()
	{
		$isComponentsEnabled = Configuration::get(Config::MOLLIE_IFRAME);
		$isSingleClickPaymentEnabled = Configuration::get(Config::MOLLIE_SINGLE_CLICK_PAYMENT);

		return !$isComponentsEnabled && $isSingleClickPaymentEnabled;
	}

	private function isVoucherPaymentAvailable($totalOrderCost)
	{
		if (NumberUtility::isLowerThan($totalOrderCost, Config::MOLLIE_VOUCHER_MINIMAL_AMOUNT)) {
			return false;
		}

		return true;
	}
}
