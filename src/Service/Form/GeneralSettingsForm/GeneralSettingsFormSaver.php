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
        $savedPaymentMethods = [];

        foreach ($this->apiService->getMethodsForConfig($this->module->api, $this->module->getPathUri()) as $method) {
            $savedPaymentMethods[] = $this->paymentMethodConfigurationUpdater->updatePaymentMethodConfiguration($method);
        }

        $success &= $this->paymentMethodRepository->deleteOldPaymentMethods(
            $savedPaymentMethods,
            Configuration::get(Config::MOLLIE_ENVIRONMENT)
        );

        $paymentOptionPositions = $this->toolsAdapter->getValue(Config::MOLLIE_FORM_PAYMENT_OPTION_POSITION);
        if ($paymentOptionPositions) {
            $success &= (bool) $this->paymentMethodPositionHandler->savePositions($paymentOptionPositions);
        }

        $success &= Configuration::updateValue(Config::MOLLIE_IFRAME, $this->toolsAdapter->getValue(Config::MOLLIE_IFRAME));
        $success &= Configuration::updateValue(Config::MOLLIE_SINGLE_CLICK_PAYMENT, $this->toolsAdapter->getValue(Config::MOLLIE_SINGLE_CLICK_PAYMENT));
        $success &= Configuration::updateValue(Config::MOLLIE_ISSUERS, $this->toolsAdapter->getValue(Config::MOLLIE_ISSUERS));

        if ($this->toolsAdapter->getValue(Config::METHODS_CONFIG) && json_decode($this->toolsAdapter->getValue(Config::METHODS_CONFIG))) {
            $success &= Configuration::updateValue(
                Config::METHODS_CONFIG,
                json_encode(@json_decode($this->toolsAdapter->getValue(Config::METHODS_CONFIG)))
            );
        }

        return (bool) $success;
    }
}