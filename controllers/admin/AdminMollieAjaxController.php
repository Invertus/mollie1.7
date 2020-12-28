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

use Mollie\Builder\ApiTestFeedbackBuilder;
use Mollie\Config\Config;
use Mollie\Exception\OrderTotalRestrictionException;
use Mollie\Handler\OrderTotal\OrderTotalUpdaterHandlerInterface;
use Mollie\Provider\CreditCardLogoProvider;
use Mollie\Repository\PaymentMethodRepository;
use Mollie\Service\ApiService;
use Mollie\Service\CountryService;
use Mollie\Service\ExceptionService;
use Mollie\Service\MollieOrderInfoService;
use Mollie\Service\MolliePaymentMailService;
use Mollie\Utility\TimeUtility;

class AdminMollieAjaxController extends ModuleAdminController
{
	/** @var Mollie */
	public $module;

	public function postProcess()
	{
		$action = Tools::getValue('action');
		switch ($action) {
			case 'togglePaymentMethod':
				$this->togglePaymentMethod();
				break;
			case 'resendPaymentMail':
				$this->resendPaymentMail();
				break;
			case 'testApiKeys':
				$this->testApiKeys();
				break;
			case 'closeUpgradeNotice':
				$this->closeUpgradeNotice();
				break;
			case 'validateLogo':
				$this->validateLogo();
				break;
			case 'refreshOrderTotalRestriction':
				$this->refreshOrderTotalRestriction();
				break;
			case 'MollieMethodConfig':
				$this->displayAjaxMollieMethodConfig();
				break;
			case 'MollieOrderInfo':
				$this->displayAjaxMollieOrderInfo();
				break;
			default:
				break;
		}
	}

	public function displayAjaxMollieOrderInfo()
	{
		header('Content-Type: application/json;charset=UTF-8');

		/** @var MollieOrderInfoService $orderInfoService */
		$orderInfoService = $this->module->getMollieContainer(MollieOrderInfoService::class);

		$input = @json_decode(Tools::file_get_contents('php://input'), true);

		$this->ajaxDie(json_encode($orderInfoService->displayMollieOrderInfo($input)));
	}

	/**
	 * Used in orderGrid.tpl to update configuration values
	 *
	 * @throws PrestaShopException
	 */
	private function displayAjaxMollieMethodConfig()
	{
		header('Content-Type: application/json;charset=UTF-8');
		/** @var ApiService $apiService */
		$apiService = $this->module->getMollieContainer(ApiService::class);
		/** @var CountryService $countryService */
		$countryService = $this->module->getMollieContainer(CountryService::class);
		$methodsForConfig = [];
		try {
			$methodsForConfig = $apiService->getMethodsForConfig($this->module->api, $this->module->getPathUri());
		} catch (MolliePrefix\Mollie\Api\Exceptions\ApiException $e) {
			$this->ajaxDie(json_encode([
				'success' => false,
				'methods' => null,
				'message' => $e->getMessage(),
			]));
		} catch (PrestaShopException $e) {
			$this->ajaxDie(json_encode([
				'success' => false,
				'methods' => null,
				'message' => $e->getMessage(),
			]));
		}
		Configuration::updateValue(Mollie\Config\Config::MOLLIE_METHODS_LAST_CHECK, Mollie\Utility\TimeUtility::getCurrentTimeStamp());
		if (!is_array($methodsForConfig)) {
			$this->ajaxDie(json_encode([
				'success' => false,
				'methods' => null,
				'message' => $this->l('No payment methods found'),
			]));
		}

		$dbMethods = @json_decode(Configuration::get(Mollie\Config\Config::METHODS_CONFIG), true);

		// Auto update images and issuers
		$shouldSave = false;
		if (is_array($dbMethods)) {
			foreach ($dbMethods as $index => &$dbMethod) {
				$found = false;
				foreach ($methodsForConfig as $methodForConfig) {
					if ($dbMethod['id'] === $methodForConfig['id']) {
						$found = true;
						foreach (['issuers', 'image', 'name', 'available'] as $prop) {
							if (isset($methodForConfig[$prop])) {
								$dbMethod[$prop] = $methodForConfig[$prop];
								$shouldSave = true;
							}
						}
						break;
					}
				}
				if (!$found) {
					unset($dbMethods[$index]);
					$shouldSave = true;
				}
			}
		} else {
			$shouldSave = true;
			$dbMethods = [];
			foreach ($methodsForConfig as $index => $method) {
				$dbMethods[] = array_merge(
					$method,
					[
						'position' => $index,
					]
				);
			}
		}

		if ($shouldSave && !empty($dbMethods)) {
			Configuration::updateValue(Mollie\Config\Config::METHODS_CONFIG, json_encode($dbMethods));
		}

		$this->ajaxDie(json_encode([
			'success' => true,
			'methods' => $methodsForConfig,
			'countries' => $countryService->getActiveCountriesList(),
		]));
	}

	/**
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	private function togglePaymentMethod()
	{
		$paymentMethod = Tools::getValue('paymentMethod');
		$paymentStatus = Tools::getValue('status');

		/** @var PaymentMethodRepository $paymentMethodRepo */
		$paymentMethodRepo = $this->module->getMollieContainer(PaymentMethodRepository::class);
		$environment = (int) Configuration::get(Mollie\Config\Config::MOLLIE_ENVIRONMENT);
		$methodId = $paymentMethodRepo->getPaymentMethodIdByMethodId($paymentMethod, $environment);
		$method = new MolPaymentMethod($methodId);
		switch ($paymentStatus) {
			case 'deactivate':
				$method->enabled = false;
				break;
			case 'activate':
				$method->enabled = true;
				break;
		}
		$method->update();

		$this->ajaxDie(json_encode(
			[
				'success' => true,
				'paymentStatus' => (int) $method->enabled,
			]
		));
	}

	/**
	 * @throws PrestaShopException
	 */
	private function resendPaymentMail()
	{
		$orderId = Tools::getValue('id_order');

		/** @var MolliePaymentMailService $molliePaymentMailService */
		$molliePaymentMailService = $this->module->getMollieContainer(MolliePaymentMailService::class);

		$response = $molliePaymentMailService->sendSecondChanceMail($orderId);

		$this->ajaxDie(json_encode($response));
	}

	/**
	 * @throws PrestaShopException
	 * @throws SmartyException
	 */
	private function testApiKeys()
	{
		$testKey = Tools::getValue('testKey');
		$liveKey = Tools::getValue('liveKey');

		/** @var ApiTestFeedbackBuilder $apiTestFeedbackBuilder */
		$apiTestFeedbackBuilder = $this->module->getMollieContainer(ApiTestFeedbackBuilder::class);
		$apiTestFeedbackBuilder->setTestKey($testKey);
		$apiTestFeedbackBuilder->setLiveKey($liveKey);
		$apiKeysTestInfo = $apiTestFeedbackBuilder->buildParams();

		$this->context->smarty->assign($apiKeysTestInfo);
		$this->ajaxDie(json_encode(
			[
				'template' => $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/api_test_results.tpl'),
			]
		));
	}

	/**
	 * @throws PrestaShopException
	 */
	private function refreshOrderTotalRestriction()
	{
		/** @var OrderTotalUpdaterHandlerInterface $orderTotalRestrictionService */
		$orderTotalRestrictionService = $this->module->getMollieContainer(OrderTotalUpdaterHandlerInterface::class);

		/** @var ExceptionService $exceptionService */
		$exceptionService = $this->module->getMollieContainer(ExceptionService::class);

		$this->context->smarty->assign([
			'refreshOrderTotalInfoStatus' => true,
			'errorMessage' => '',
		]);

		try {
			$orderTotalRestrictionService->handleOrderTotalUpdate();
		} catch (OrderTotalRestrictionException $orderTotalRestrictionException) {
			$errorMessage = $exceptionService->getErrorMessageForException(
				$orderTotalRestrictionException,
				$exceptionService->getErrorMessages()
			);

			$this->context->smarty->assign([
				'refreshOrderTotalInfoStatus' => false,
				'errorMessage' => $errorMessage,
			]);
		}

		$this->ajaxDie(json_encode([
			'template' => $this->context->smarty->fetch(
				$this->module->getLocalPath() . 'views/templates/admin/order_total_refresh_results.tpl'
			),
		]));
	}

	private function closeUpgradeNotice()
	{
		Configuration::updateValue(Config::MOLLIE_MODULE_UPGRADE_NOTICE_CLOSE_DATE, TimeUtility::getNowTs());
	}

	private function validateLogo()
	{
		/** @var CreditCardLogoProvider $creditCardLogoProvider */
		$creditCardLogoProvider = $this->module->getMollieContainer(CreditCardLogoProvider::class);
		$target_file = $creditCardLogoProvider->getLocalLogoPath();
		$isUploaded = 1;
		$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
		$returnText = '';
		// Check image format
		if ('jpg' !== $imageFileType && 'png' !== $imageFileType) {
			$returnText = $this->l('Sorry, only JPG, PNG files are allowed.');
			$isUploaded = 0;
		}

		if (1 === $isUploaded) {
			//  if everything is ok, try to upload file
			if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
				$returnText = basename($_FILES['fileToUpload']['name']);
			} else {
				$isUploaded = 0;
				$returnText = $this->l('Sorry, there was an error uploading your logo.');
			}
		}

		echo json_encode(['status' => $isUploaded, 'message' => $returnText]);
	}
}
