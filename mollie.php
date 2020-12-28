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
if (!include_once (dirname(__FILE__) . '/vendor/autoload.php')) {
	return;
}
if (!include_once (dirname(__FILE__) . '/vendor/guzzlehttp/guzzle/src/functions_include.php')) {
	return;
}
if (!include_once (dirname(__FILE__) . '/vendor/guzzlehttp/promises/src/functions_include.php')) {
	return;
}
if (!include_once (dirname(__FILE__) . '/vendor/guzzlehttp/psr7/src/functions_include.php')) {
	return;
}

/**
 * Class Mollie.
 *
 * // Translation detection:
 * $this->l('Shipping);
 * $this->l('Gift wrapping');
 */
class Mollie extends PaymentModule
{
	/**
	 * Symfony DI Container.
	 **/
	private $moduleContainer;

	const DISABLE_CACHE = true;

	/** @var MolliePrefix\Mollie\Api\MollieApiClient|null */
	public $api = null;

	/** @var string */
	public $currentOrderReference;

	/** @var string */
	public static $selectedApi;

	/** @var bool Indicates whether the Smarty cache has been cleared during updates */
	public static $cacheCleared;

	// The Addons version does not include the GitHub updater
	const ADDONS = false;

	const SUPPORTED_PHP_VERSION = '5.6';

	const ADMIN_MOLLIE_PARENT_CONTROLLER = 'AdminMollieParent';
	const ADMIN_MOLLIE_MODULE_CONTROLLER = 'AdminMollieModule';
	const ADMIN_MOLLIE_CREDENTIALS_CONTROLLER = 'AdminMollieCredentials';
	const ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER = 'AdminMollieGeneralSettings';
	const ADMIN_MOLLIE_ADVANCED_SETTINGS_CONTROLLER = 'AdminMollieAdvancedSettings';
	const ADMIN_MOLLIE_AJAX_CONTROLLER = 'AdminMollieAjax';

	/**
	 * Mollie constructor.
	 */
	public function __construct()
	{
		$this->name = 'mollie';
		$this->tab = 'payments_gateways';
		$this->version = '4.2.0';
		$this->author = 'Mollie B.V.';
		$this->need_instance = 1;
		$this->bootstrap = true;
		$this->module_key = 'a48b2f8918358bcbe6436414f48d8915';

		parent::__construct();
		$this->ps_versions_compliancy = ['min' => '1.6.1.0', 'max' => _PS_VERSION_];
		$this->displayName = $this->l('Mollie');
		$this->description = $this->l('Mollie Payments');

		if (-1 === version_compare(phpversion(), $this::SUPPORTED_PHP_VERSION)) {
			return;
		}

		$this->compile();
		$this->setApiKey();
	}

	/**
	 * Installs the Mollie Payments Module.
	 *
	 * @return bool
	 */
	public function install()
	{
		if (-1 === version_compare(phpversion(), Mollie\Config\Config::SUPPORTED_PHP_VERSION)) {
			$this->_errors[] = $this->l('Dear customer, your PHP version is too low. Please upgrade your PHP version to use this module. Mollie module supports PHP 5.6 and higher versions.');

			return false;
		}

		if (!parent::install()) {
			$this->_errors[] = $this->l('Unable to install module');

			return false;
		}

		/** @var \Mollie\Install\Installer $installer */
		$installer = $this->getMollieContainer(\Mollie\Install\Installer::class);
		if (!$installer->install()) {
			$this->_errors = array_merge($this->_errors, $installer->getErrors());

			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function uninstall()
	{
		/** @var \Mollie\Install\Uninstall $uninstall */
		$uninstall = $this->getMollieContainer(\Mollie\Install\Uninstall::class);
		if (!$uninstall->uninstall()) {
			$this->_errors[] = $uninstall->getErrors();

			return false;
		}

		return parent::uninstall();
	}

	private function compile()
	{
		if (!class_exists('MolliePrefix\Symfony\Component\DependencyInjection\ContainerBuilder')) {
			// If you wonder why this happens then this problem occurs in rare case when upgrading mollie from old versions
			// where dependency injection container was without "MolliePrefix".
			// On Upgrade PrestaShop cached previous vendor thus causing missing class issues - the only way is to convince
			// merchant to try installing again where.
			$isAdmin = $this->context->controller instanceof AdminController;

			if ($isAdmin) {
				http_response_code(500);
				exit(
				$this->l('The module upload requires an extra refresh. Please upload the Mollie module ZIP file once again. If you still get this error message after attempting another upload, please contact Mollie support with this screenshot and they will guide through the next steps: info@mollie.com')
				);
			}
		}

		$containerBuilder = new MolliePrefix\Symfony\Component\DependencyInjection\ContainerBuilder();
		$locator = new MolliePrefix\Symfony\Component\Config\FileLocator($this->getLocalPath() . 'config');
		$loader = new MolliePrefix\Symfony\Component\DependencyInjection\Loader\YamlFileLoader($containerBuilder, $locator);
		$loader->load('config.yml');
		$containerBuilder->compile();

		$this->moduleContainer = $containerBuilder;
	}

	/**
	 * @param string|bool $id
	 *
	 * @return mixed
	 */
	public function getMollieContainer($id = false)
	{
		if ($id) {
			return $this->moduleContainer->get($id);
		}

		return $this->moduleContainer;
	}

	/**
	 * @return mixed
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getIdentifier()
	{
		return $this->identifier;
	}

	public function getContent()
	{
		if (\Mollie\Utility\EnvironmentUtility::getApiKey()) {
			Tools::redirectAdmin($this->context->link->getAdminLink(self::ADMIN_MOLLIE_GENERAL_SETTINGS_CONTROLLER));
		}

		Tools::redirectAdmin($this->context->link->getAdminLink(self::ADMIN_MOLLIE_CREDENTIALS_CONTROLLER));
	}

	/**
	 * @param string $str
	 *
	 * @return string
	 *
	 * @deprecated
	 */
	public function lang($str)
	{
		/** @var Mollie\Service\LanguageService $langService */
		$langService = $this->getMollieContainer(Mollie\Service\LanguageService::class);
		$lang = $langService->getLang();
		if (array_key_exists($str, $lang)) {
			return $lang[$str];
		}

		return $str;
	}

	/**
	 * @throws PrestaShopException
	 */
	public function hookActionFrontControllerSetMedia()
	{
		/** @var \Mollie\Service\ErrorDisplayService $errorDisplayService */
		$errorDisplayService = $this->getMollieContainer()->get(\Mollie\Service\ErrorDisplayService::class);

		$isOrderController = $this->context->controller instanceof OrderControllerCore;
		$isOPCController = $this->context->controller instanceof OrderOpcControllerCore;
		$isCartController = $this->context->controller instanceof CartControllerCore;
		if ($isOrderController || $isOPCController) {
			$errorDisplayService->showCookieError('mollie_payment_canceled_error');

			Media::addJsDef([
				'profileId' => Configuration::get(Mollie\Config\Config::MOLLIE_PROFILE_ID),
				'isoCode' => $this->context->language->language_code,
				'isTestMode' => \Mollie\Config\Config::isTestMode(),
			]);
			if (\Mollie\Config\Config::isVersion17()) {
				$this->context->controller->registerJavascript(
					'mollie_iframe_js',
					'https://js.mollie.com/v1/mollie.js',
					['server' => 'remote', 'position' => 'bottom', 'priority' => 150]
				);
				$this->context->controller->addJS("{$this->_path}views/js/front/mollie_iframe.js");
			} else {
				$this->context->controller->addMedia('https://js.mollie.com/v1/mollie.js', null, null, false, false);
				$this->context->controller->addJS("{$this->_path}views/js/front/mollie_iframe_16.js");
				$this->context->controller->addJS("{$this->_path}views/js/front/mollie_payment_method_click_lock_16.js");
			}
			Media::addJsDef([
				'ajaxUrl' => $this->context->link->getModuleLink('mollie', 'ajax'),
				'isPS17' => \Mollie\Config\Config::isVersion17(),
			]);
			$this->context->controller->addJS("{$this->_path}views/js/front/mollie_error_handle.js");
			$this->context->controller->addCSS("{$this->_path}views/css/mollie_iframe.css");
			if (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
				$this->context->controller->addJS($this->getPathUri() . 'views/js/apple_payment.js');
			}
			$this->context->smarty->assign([
				'custom_css' => Configuration::get(Mollie\Config\Config::MOLLIE_CSS),
			]);

			$this->context->controller->addJS("{$this->_path}views/js/front/payment_fee.js");

			return $this->display(__FILE__, 'views/templates/front/custom_css.tpl');
		}

		if ($isCartController) {
			$errorDisplayService->showCookieError('mollie_payment_canceled_error');
		}
	}

	/**
	 * Add custom JS && CSS to admin controllers.
	 */
	public function hookActionAdminControllerSetMedia()
	{
		$currentController = Tools::getValue('controller');

		if ('AdminOrders' === $currentController) {
			Media::addJsDef([
				'mollieHookAjaxUrl' => $this->context->link->getAdminLink('AdminMollieAjax'),
			]);
			$this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/order-list.css');
			$this->context->controller->addJS($this->getPathUri() . 'views/js/admin/order_list.js');

			if (Tools::isSubmit('addorder') || version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
				Media::addJsDef([
					'molliePendingStatus' => Configuration::get(\Mollie\Config\Config::MOLLIE_STATUS_AWAITING),
					'isPsVersion177' => version_compare(_PS_VERSION_, '1.7.7.0', '>='),
				]);
				$this->context->controller->addJS($this->getPathUri() . 'views/js/admin/order_add.js');
			}
		}
	}

	/**
	 * @throws PrestaShopException
	 */
	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/menu.css');

		$html = '';
		if ($this->context->controller->controller_name === 'AdminOrders') {
			$this->context->smarty->assign([
				'mollieProcessUrl' => $this->context->link->getAdminLink(self::ADMIN_MOLLIE_AJAX_CONTROLLER, true),
				'mollieCheckMethods' => Mollie\Utility\TimeUtility::getCurrentTimeStamp() > ((int) Configuration::get(Mollie\Config\Config::MOLLIE_METHODS_LAST_CHECK) + Mollie\Config\Config::MOLLIE_METHODS_CHECK_INTERVAL),
			]);
			$html .= $this->display(__FILE__, 'views/templates/admin/ordergrid.tpl');
			if (Tools::isSubmit('addorder') || version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
				$html .= $this->display($this->getPathUri(), 'views/templates/admin/email_checkbox.tpl');
			}
		}

		return $html;
	}

	/**
	 * @param array $params Hook parameters
	 *
	 * @return string|bool Hook HTML
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function hookDisplayAdminOrder($params)
	{
		/** @var \Mollie\Repository\PaymentMethodRepository $paymentMethodRepo */
		$paymentMethodRepo = $this->getMollieContainer(\Mollie\Repository\PaymentMethodRepositoryInterface::class);

		/** @var \Mollie\Service\ShipmentServiceInterface $shipmentService */
		$shipmentService = $this->getMollieContainer(\Mollie\Service\ShipmentService::class);

		$cartId = Cart::getCartIdByOrderId((int) $params['id_order']);
		$transaction = $paymentMethodRepo->getPaymentBy('cart_id', (string) $cartId);
		if (empty($transaction)) {
			return false;
		}
		$currencies = [];
		foreach (Currency::getCurrencies() as $currency) {
			$currencies[Tools::strtoupper($currency['iso_code'])] = [
				'name' => $currency['name'],
				'iso_code' => Tools::strtoupper($currency['iso_code']),
				'sign' => $currency['sign'],
				'blank' => (bool) isset($currency['blank']) ? $currency['blank'] : true,
				'format' => (int) $currency['format'],
				'decimals' => (bool) isset($currency['decimals']) ? $currency['decimals'] : true,
			];
		}

		$order = new Order($params['id_order']);
		$this->context->smarty->assign([
			'ajaxEndpoint' => $this->context->link->getAdminLink(self::ADMIN_MOLLIE_AJAX_CONTROLLER, true) . '&ajax=1&action=MollieOrderInfo',
			'transactionId' => $transaction['transaction_id'],
			'currencies' => $currencies,
			'tracking' => $shipmentService->getShipmentInformation($order->reference),
			'publicPath' => __PS_BASE_URI__ . 'modules/' . basename(__FILE__, '.php') . '/views/js/dist/',
			'webPackChunks' => \Mollie\Utility\UrlPathUtility::getWebpackChunks('app'),
			'errorDisplay' => Configuration::get(Mollie\Config\Config::MOLLIE_DISPLAY_ERRORS),
		]);

		return $this->display(__FILE__, 'order_info.tpl');
	}

	/**
	 * @return string
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function hookDisplayPayment()
	{
		$smarty = $this->context->smarty;
		$issuerSetting = Configuration::get(Mollie\Config\Config::MOLLIE_ISSUERS);

		/** @var \Mollie\Service\PaymentMethodService $paymentMethodService */
		$paymentMethodService = $this->getMollieContainer(\Mollie\Service\PaymentMethodService::class);

		/** @var \Mollie\Service\IssuerService $issuerService */
		$issuerService = $this->getMollieContainer(\Mollie\Service\IssuerService::class);

		/** @var \Mollie\Service\OrderFeeService $orderFeeService */
		$orderFeeService = $this->getMollieContainer(\Mollie\Service\OrderFeeService::class);

		$apiMethods = $paymentMethodService->getMethodsForCheckout();
		$issuerList = [];
		foreach ($apiMethods as $apiMethod) {
			if (MolliePrefix\Mollie\Api\Types\PaymentMethod::IDEAL === $apiMethod['id_method']) {
				$issuerList = $issuerService->getIdealIssuers();
			}
		}
		$apiMethods = $orderFeeService->getPaymentFees($apiMethods, $this->context->cart->getOrderTotal());

		$isIFrameEnabled = Configuration::get(Mollie\Config\Config::MOLLIE_IFRAME);
		/** @var Cart $cart */
		$cart = Context::getContext()->cart;
		$smarty->assign([
			'mollieIframe' => $isIFrameEnabled,
			'link' => $this->context->link,
			'cartAmount' => (int) ($cart->getOrderTotal(true) * 100),
			'methods' => $apiMethods,
			'issuers' => $issuerList,
			'issuer_setting' => $issuerSetting,
			'images' => Configuration::get(Mollie\Config\Config::MOLLIE_IMAGES),
			'warning' => $this->warning,
			'msg_pay_with' => $this->l('Pay with %s'),
			'msg_bankselect' => $this->l('Select your bank:'),
			'module' => $this,
			'publicPath' => __PS_BASE_URI__ . 'modules/' . basename(__FILE__, '.php') . '/views/js/dist/',
			'CARTES_BANCAIRES' => Mollie\Config\Config::CARTES_BANCAIRES,
			'ISSUERS_ON_CLICK' => Mollie\Config\Config::ISSUERS_ON_CLICK,
			'web_pack_chunks' => Mollie\Utility\UrlPathUtility::getWebpackChunks('app'),
			'display_errors' => Mollie\Config\Config::MOLLIE_DISPLAY_ERRORS,
			'mollie_translations' => [
				'chooseYourBank' => $this->l('Choose your bank'),
				'orPayByIdealQr' => $this->l('or pay by iDEAL QR'),
				'choose' => $this->l('Choose'),
				'cancel' => $this->l('Cancel'),
			],
		]);

		$iframeDisplay = '';
		if (!\Mollie\Config\Config::isVersion17() && $isIFrameEnabled) {
			$iframeDisplay = $this->display(__FILE__, 'mollie_iframe_16.tpl');
		}

		return $this->display(__FILE__, 'addjsdef.tpl') . $this->display(__FILE__, 'payment.tpl') . $iframeDisplay;
	}

	/**
	 * EU Advanced Compliance module (PrestaShop module) Advanced Checkout option enabled.
	 *
	 * @return array|null
	 *
	 * @throws PrestaShopException
	 */
	public function hookDisplayPaymentEU()
	{
		// Please update your one page checkout module if it depends on `displayPaymentEU`
		// Mollie does no longer support this hook on PresaShop v1.7 or higher
		// due to the problems caused by mixing the hooks `paymentOptions` and `displayPaymentEU`
		// Only uncomment the following three lines if you have no other choice:
		if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			return [];
		}

		/** @var \Mollie\Service\PaymentMethodService $paymentMethodService */
		$paymentMethodService = $this->getMollieContainer(\Mollie\Service\PaymentMethodService::class);

		$methods = $paymentMethodService->getMethodsForCheckout();

		$context = Context::getContext();
		$iso = Tools::strtolower($context->currency->iso_code);
		$paymentOptions = [];

		foreach ($methods as $method) {
			if (!isset(Mollie\Config\Config::$methodCurrencies[$method['id_method']])) {
				continue;
			}
			if (!in_array($iso, Mollie\Config\Config::$methodCurrencies[$method['id_method']])) {
				continue;
			}
			$images = json_decode($method['images_json'], true);
			$paymentOptions[] = [
				'cta_text' => $this->l($method['method_name'], \Mollie\Service\LanguageService::FILE_NAME),
				'logo' => Mollie\Config\Config::LOGOS_NORMAL === Configuration::get(Mollie\Config\Config::MOLLIE_IMAGES)
					? $images['size1x']
					: $images['size2x'],
				'action' => $this->context->link->getModuleLink(
					'mollie',
					'payment',
					['method' => $method['id_method'], 'rand' => Mollie\Utility\TimeUtility::getCurrentTimeStamp()],
					true
				),
			];
		}

		return $paymentOptions;
	}

	/**
	 * @param array $params
	 *
	 * @return array|null
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function hookPaymentOptions($params)
	{
		if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
			return [];
		}
		$paymentOptions = [];

		/** @var \Mollie\Repository\PaymentMethodRepositoryInterface $paymentMethodRepository */
		$paymentMethodRepository = $this->getMollieContainer(\Mollie\Repository\PaymentMethodRepositoryInterface::class);

		/** @var \Mollie\Handler\PaymentOption\PaymentOptionHandlerInterface $paymentOptionsHandler */
		$paymentOptionsHandler = $this->getMollieContainer(\Mollie\Handler\PaymentOption\PaymentOptionHandlerInterface::class);

		/** @var \Mollie\Service\PaymentMethodService $paymentMethodService */
		$paymentMethodService = $this->getMollieContainer(\Mollie\Service\PaymentMethodService::class);

		$methods = $paymentMethodService->getMethodsForCheckout();

		foreach ($methods as $method) {
			/** @var MolPaymentMethod|null $paymentMethod */
			$paymentMethod = $paymentMethodRepository->findOneBy(['id_payment_method' => (int) $method['id_payment_method']]);

			if (!$paymentMethod) {
				continue;
			}
			$paymentOptions[] = $paymentOptionsHandler->handle($paymentMethod);
		}

		return $paymentOptions;
	}

	/**
	 * @return string
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function hookDisplayOrderConfirmation()
	{
		/** @var \Mollie\Repository\PaymentMethodRepository $paymentMethodRepo */
		$paymentMethodRepo = $this->getMollieContainer(\Mollie\Repository\PaymentMethodRepository::class);
		$payment = $paymentMethodRepo->getPaymentBy('cart_id', (string) Tools::getValue('id_cart'));
		if ($payment && MolliePrefix\Mollie\Api\Types\PaymentStatus::STATUS_PAID == $payment['bank_status']) {
			$this->context->smarty->assign('okMessage', $this->l('Thank you. Your payment has been received.'));

			return $this->display(__FILE__, 'ok.tpl');
		}

		return '';
	}

	/**
	 * actionOrderStatusUpdate hook.
	 *
	 * @param array $params
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 *
	 * @since 3.3.0
	 */
	public function hookActionOrderStatusUpdate($params = [])
	{
		if (!isset($params['newOrderStatus']) || !isset($params['id_order'])) {
			return;
		}

		if ($params['newOrderStatus'] instanceof OrderState) {
			$orderStatus = $params['newOrderStatus'];
		} else {
			$orderStatus = new OrderState((int) $params['newOrderStatus']);
		}
		$order = new Order($params['id_order']);

		if (!Validate::isLoadedObject($orderStatus)) {
			return;
		}

		if (!Validate::isLoadedObject($order)) {
			return;
		}

		/** @var \Mollie\Handler\Shipment\ShipmentSenderHandlerInterface $shipmentSenderHandler */
		$shipmentSenderHandler = $this->getMollieContainer(
			Mollie\Handler\Shipment\ShipmentSenderHandlerInterface::class
		);

		$shipmentSenderHandler->handleShipmentSender($this->api, $order, $orderStatus);
	}

	/**
	 * @param array $params
	 *
	 * @return bool
	 *
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
	 */
	public function hookActionEmailSendBefore($params)
	{
		if (!isset($params['cart']->id)) {
			return true;
		}

		$cart = new Cart($params['cart']->id);
		$orderId = Order::getOrderByCartId($cart->id);
		$order = new Order($orderId);
		if ($order->module !== $this->name) {
			return true;
		}
		/** @var \Mollie\Validator\OrderConfMailValidator $orderConfMailValidator */
		$orderConfMailValidator = $this->getMollieContainer(\Mollie\Validator\OrderConfMailValidator::class);

		/** @var \Mollie\Validator\NewOrderMailValidator $newOrderMailValidator */
		$newOrderMailValidator = $this->getMollieContainer(\Mollie\Validator\NewOrderMailValidator::class);

		/** @var string $template */
		$template = $params['template'];

		if ('order_conf' === $template) {
			return $orderConfMailValidator->validate((int) $order->current_state);
		}

		if ('new_order' === $template) {
			return $newOrderMailValidator->validate((int) $order->current_state);
		}

		if ('order_conf' === $template ||
			'account' === $template ||
			'backoffice_order' === $template ||
			'contact_form' === $template ||
			'credit_slip' === $template ||
			'in_transit' === $template ||
			'order_changed' === $template ||
			'order_merchant_comment' === $template ||
			'order_return_state' === $template ||
			'cheque' === $template ||
			'payment' === $template ||
			'preparation' === $template ||
			'shipped' === $template ||
			'order_canceled' === $template ||
			'payment_error' === $template ||
			'outofstock' === $template ||
			'bankwire' === $template ||
			'refund' === $template) {
			$orderId = Order::getOrderByCartId($cart->id);
			$order = new Order($orderId);
			if (!Validate::isLoadedObject($order)) {
				return true;
			}
			try {
				$orderFee = new MolOrderFee($order->id);
			} catch (Exception $e) {
				PrestaShopLogger::addLog(__METHOD__ . ' said: ' . $e->getMessage(), Mollie\Config\Config::CRASH);

				return true;
			}
			if ($orderFee->order_fee) {
				$params['templateVars']['{payment_fee}'] = Tools::displayPrice($orderFee->order_fee);
			} else {
				$params['templateVars']['{payment_fee}'] = Tools::displayPrice(0);
			}
		}

		return true;
	}

	public function hookDisplayPDFInvoice($params)
	{
		if (!$params['object'] instanceof OrderInvoice) {
			return;
		}

		/** @var \Mollie\Builder\InvoicePdfTemplateBuilder $invoiceTemplateBuilder */
		$invoiceTemplateBuilder = $this->getMollieContainer(\Mollie\Builder\InvoicePdfTemplateBuilder::class);

		$templateParams = $invoiceTemplateBuilder
			->setOrder($params['object']->getOrder())
			->buildParams()
		;

		if (empty($templateParams)) {
			return;
		}

		$this->context->smarty->assign($templateParams);

		return $this->context->smarty->fetch(
			$this->getLocalPath() . 'views/templates/admin/invoice_fee.tpl'
		);
	}

	/**
	 * @return array
	 */
	public function getTabs()
	{
		/** @var \Mollie\Provider\TabProvider $tabProvider */
		$tabProvider = $this->getMollieContainer(\Mollie\Provider\TabProvider::class);

		return $tabProvider->getModuleTabs();
	}

	public function hookActionAdminOrdersListingFieldsModifier($params)
	{
		if (isset($params['select'])) {
			$params['select'] = rtrim($params['select'], ' ,') . ' ,mol.`transaction_id`';
		}
		if (isset($params['join'])) {
			$params['join'] .= ' LEFT JOIN `' . _DB_PREFIX_ . 'mollie_payments` mol ON mol.`order_reference` = a.`reference`';
		}
		$params['fields']['order_id'] = [
			'title' => $this->l('Resend payment link'),
			'align' => 'text-center',
			'class' => 'fixed-width-xs',
			'orderby' => false,
			'search' => false,
			'remove_onclick' => true,
			'callback_object' => 'mollie',
			'callback' => 'resendOrderPaymentLink',
		];
	}

	public function hookActionOrderGridDefinitionModifier(array $params)
	{
		/** @var \Mollie\Grid\Definition\Modifier\OrderGridDefinitionModifier $orderGridDefinitionModifier */
		$orderGridDefinitionModifier = $this->getMollieContainer(\Mollie\Grid\Definition\Modifier\OrderGridDefinitionModifier::class);
		$gridDefinition = $params['definition'];

		$orderGridDefinitionModifier->modify($gridDefinition);
	}

	public function hookActionOrderGridQueryBuilderModifier(array $params)
	{
		/** @var \Mollie\Grid\Query\Modifier\OrderGridQueryModifier $orderGridQueryModifier */
		$orderGridQueryModifier = $this->getMollieContainer(\Mollie\Grid\Query\Modifier\OrderGridQueryModifier::class);
		$searchQueryBuilder = $params['search_query_builder'];

		$orderGridQueryModifier->modify($searchQueryBuilder);
	}

	public function hookActionValidateOrder($params)
	{
		if ('admin' !== $this->context->controller->controller_type) {
			return;
		}
		if ('AdminOrders' === $this->context->controller->controller_name &&
			$params['order']->module === $this->name
		) {
			$cartId = $params['cart']->id;
			$totalPaid = strval($params['order']->total_paid);
			$currency = $params['currency']->iso_code;
			$customerKey = $params['customer']->secure_key;
			$orderReference = $params['order']->reference;
			$orderPayment = $params['order']->payment;
			$orderId = $params['order']->id;

			/** @var \Mollie\Service\PaymentMethodService $paymentMethodService */
			$paymentMethodService = $this->getMollieContainer(\Mollie\Service\PaymentMethodService::class);
			$paymentMethodObj = new MolPaymentMethod();
			$paymentData = $paymentMethodService->getPaymentData(
				$totalPaid,
				$currency,
				'',
				null,
				$cartId,
				$customerKey,
				$paymentMethodObj,
				false,
				$orderReference
			);

			$newPayment = $this->api->payments->create($paymentData->jsonSerialize());

			/** @var \Mollie\Repository\PaymentMethodRepository $paymentMethodRepository */
			$paymentMethodRepository = $this->getMollieContainer(\Mollie\Repository\PaymentMethodRepository::class);
			$paymentMethodRepository->addOpenStatusPayment(
				$cartId,
				$orderPayment,
				$newPayment->id,
				$orderId,
				$orderReference
			);

			$sendMolliePaymentMail = Tools::getValue('mollie-email-send');
			if ('on' === $sendMolliePaymentMail) {
				/** @var \Mollie\Service\MolliePaymentMailService $molliePaymentMailService */
				$molliePaymentMailService = $this->getMollieContainer(\Mollie\Service\MolliePaymentMailService::class);
				$molliePaymentMailService->sendSecondChanceMail($orderId);
			}
		}
	}

	/**
	 * @param int $orderId
	 *
	 * @return string|bool
	 *
	 * @throws PrestaShopDatabaseException
	 */
	public static function resendOrderPaymentLink($orderId)
	{
		/** @var Mollie $module */
		$module = Module::getInstanceByName('mollie');
		/** @var \Mollie\Repository\PaymentMethodRepository $molliePaymentRepo */
		$molliePaymentRepo = $module->getMollieContainer(\Mollie\Repository\PaymentMethodRepositoryInterface::class);
		$molPayment = $molliePaymentRepo->getPaymentBy('order_id', (string) $orderId);
		if (\Mollie\Utility\MollieStatusUtility::isPaymentFinished($molPayment['bank_status'])) {
			return false;
		}

		/** @var \Mollie\Presenter\OrderListActionBuilder $orderListActionBuilder */
		$orderListActionBuilder = $module->getMollieContainer(\Mollie\Presenter\OrderListActionBuilder::class);

		return $orderListActionBuilder->buildOrderPaymentResendButton($module->smarty, $orderId);
	}

	public function hookActionAdminStatusesListingFieldsModifier($params)
	{
		if ('AdminStatuses' === Tools::getValue('controller') && isset($params['fields']['id_order_state'])) {
			$params['where'] = " AND (a.`module_name` = '{$this->name}' AND a.`deleted` = 0) OR a.`module_name` NOT LIKE '{$this->name}'";
		}

		if ('AdminStatuses' === Tools::getValue('controller') && isset($params['fields']['id_order_return_state'])) {
			$params['where'] = null;
		}
	}

	private function setApiKey()
	{
		if ($this->api) {
			return;
		}
		/** @var \Mollie\Service\ApiKeyService $apiKeyService */
		$apiKeyService = $this->getMollieContainer(\Mollie\Service\ApiKeyService::class);

		$environment = (int) Configuration::get(Mollie\Config\Config::MOLLIE_ENVIRONMENT);
		$apiKeyConfig = \Mollie\Config\Config::ENVIRONMENT_LIVE === (int) $environment ?
			Mollie\Config\Config::MOLLIE_API_KEY : Mollie\Config\Config::MOLLIE_API_KEY_TEST;

		try {
			$this->api = $apiKeyService->setApiKey(Configuration::get($apiKeyConfig), $this->version);
		} catch (MolliePrefix\Mollie\Api\Exceptions\IncompatiblePlatform $e) {
			PrestaShopLogger::addLog(__METHOD__ . ' - System incompatible: ' . $e->getMessage(), Mollie\Config\Config::CRASH);
		} catch (MolliePrefix\Mollie\Api\Exceptions\ApiException $e) {
			$this->warning = $this->l('Payment error:') . $e->getMessage();
			PrestaShopLogger::addLog(__METHOD__ . ' said: ' . $this->warning, Mollie\Config\Config::CRASH);
		}
	}
}
