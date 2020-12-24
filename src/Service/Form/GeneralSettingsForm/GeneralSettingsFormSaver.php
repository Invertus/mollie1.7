<?php

namespace Mollie\Service\Form\GeneralSettingsForm;

use Configuration;
use Mollie;
use Mollie\Adapter\ToolsAdapter;
use Mollie\Config\Config;
use Mollie\Exception\PaymentMethodConfigurationUpdaterException;
use Mollie\Handler\Settings\PaymentMethodPositionHandlerInterface;
use Mollie\Repository\PaymentMethodRepositoryInterface;
use Mollie\Service\ApiService;
use Mollie\Service\Form\FormSaver;
use Mollie\Service\PaymentMethod\PaymentMethodConfigurationUpdater;

class GeneralSettingsFormSaver implements FormSaver
{
    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    /**
     * @var Mollie
     */
    private $module;

    /**
     * @var PaymentMethodConfigurationUpdater
     */
    private $paymentMethodConfigurationUpdater;

    /**
     * @var ApiService
     */
    private $apiService;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var PaymentMethodPositionHandlerInterface
     */
    private $paymentMethodPositionHandler;

    public function __construct(
        Mollie $module,
        ToolsAdapter $toolsAdapter,
        ApiService $apiService,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodConfigurationUpdater $paymentMethodConfigurationUpdater,
        PaymentMethodPositionHandlerInterface $paymentMethodPositionHandler
    ) {
        $this->module = $module;
        $this->toolsAdapter = $toolsAdapter;
        $this->apiService = $apiService;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodConfigurationUpdater = $paymentMethodConfigurationUpdater;
        $this->paymentMethodPositionHandler = $paymentMethodPositionHandler;
    }

    /**
     * @return bool
     * @throws PaymentMethodConfigurationUpdaterException
     */
    public function saveConfiguration()
    {
        $success = true;

        $success &= $this->savePaymentMethodSettings();
        $success &= Configuration::updateValue(Config::MOLLIE_IFRAME, $this->toolsAdapter->getValue(Config::MOLLIE_IFRAME));
        $success &= Configuration::updateValue(Config::MOLLIE_SINGLE_CLICK_PAYMENT, $this->toolsAdapter->getValue(Config::MOLLIE_SINGLE_CLICK_PAYMENT));
        $success &= Configuration::updateValue(Config::MOLLIE_ISSUERS, $this->toolsAdapter->getValue(Config::MOLLIE_ISSUERS));

        return (bool) $success;
    }

    /**
     * @return bool
     * @throws PaymentMethodConfigurationUpdaterException
     */
    public function savePaymentMethodSettings()
    {
        $success = true;
        $success &= $this->deleteOldPaymentMethods();

        $paymentOptionPositions = $this->toolsAdapter->getValue(Config::MOLLIE_FORM_PAYMENT_OPTION_POSITION);

        if ($paymentOptionPositions) {
            $success &= (bool) $this->paymentMethodPositionHandler->savePositions($paymentOptionPositions);
        }
        $paymentMethodConfiguration = $this->toolsAdapter->getValue(Config::METHODS_CONFIG);

        if ($paymentMethodConfiguration && json_decode($paymentMethodConfiguration)) {
            $success &= Configuration::updateValue(
                Config::METHODS_CONFIG,
                json_encode(@json_decode($paymentMethodConfiguration))
            );
        }

        return (bool) $success;
    }

    /**
     * @return bool
     * @throws PaymentMethodConfigurationUpdaterException
     */
    private function deleteOldPaymentMethods()
    {
        $savedPaymentMethods = [];

        foreach ($this->apiService->getMethodsForConfig($this->module->api, $this->module->getPathUri()) as $method) {
            $savedPaymentMethods[] = $this->paymentMethodConfigurationUpdater->updatePaymentMethodConfiguration($method);
        }

        return $this->paymentMethodRepository->deleteOldPaymentMethods(
            $savedPaymentMethods,
            Configuration::get(Config::MOLLIE_ENVIRONMENT)
        );
    }
}