<?php

namespace Mollie\Service\PaymentMethod;

use Exception;
use Mollie\Adapter\ConfigurationAdapter;
use Mollie\Adapter\ToolsAdapter;
use Mollie\Config\Config;
use Mollie\Exception\PaymentMethodConfigurationUpdaterException;
use Mollie\Repository\CountryRepository;
use Mollie\Repository\PaymentMethodRepositoryInterface;
use Mollie\Service\EntityManager\EntityManagerInterface;
use Mollie\Service\PaymentMethodService;
use MolPaymentMethodIssuer;

class PaymentMethodConfigurationUpdater
{
	/**
	 * @var PaymentMethodRepositoryInterface
	 */
	private $paymentMethodRepository;

	/**
	 * @var CountryRepository
	 */
	private $countryRepository;

	/**
	 * @var PaymentMethodService
	 */
	private $paymentMethodService;

	/**
	 * @var ToolsAdapter
	 */
	private $toolsAdapter;

	/**
	 * @var ConfigurationAdapter
	 */
	private $configurationAdapter;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(
		ToolsAdapter $toolsAdapter,
		ConfigurationAdapter $configurationAdapter,
		PaymentMethodRepositoryInterface $paymentMethodRepository,
		CountryRepository $countryRepository,
		PaymentMethodService $paymentMethodService,
		EntityManagerInterface $entityManager
	) {
		$this->paymentMethodRepository = $paymentMethodRepository;
		$this->countryRepository = $countryRepository;
		$this->paymentMethodService = $paymentMethodService;
		$this->toolsAdapter = $toolsAdapter;
		$this->configurationAdapter = $configurationAdapter;
		$this->entityManager = $entityManager;
	}

	/**
	 * @param array $paymentMethodData
	 *
	 * @return string
	 *
	 * @throws PaymentMethodConfigurationUpdaterException
	 */
	public function updatePaymentMethodConfiguration(array $paymentMethodData)
	{
		if (empty($paymentMethodData)) {
			throw new PaymentMethodConfigurationUpdaterException('Failed to update payment method configuration. Payment method (%s) configuration was not saved to database', PaymentMethodConfigurationUpdaterException::NO_PAYMENT_METHOD_DATA_PROVIDED, $paymentMethodData['id']);
		}

		try {
			$paymentMethod = $this->paymentMethodService->savePaymentMethod($paymentMethodData);
		} catch (Exception $e) {
			throw new PaymentMethodConfigurationUpdaterException('Failed to update payment method configuration. Payment method (%s) configuration was not saved to database', PaymentMethodConfigurationUpdaterException::FAILED_TO_SAVE_PAYMENT_METHOD, $paymentMethodData['id']);
		}

		if (!$this->paymentMethodRepository->deletePaymentMethodIssuersByPaymentMethodId($paymentMethod->id)) {
			throw new PaymentMethodConfigurationUpdaterException('Failed to update payment method configuration. Payment methods (%s) old issuers unable to be deleted', PaymentMethodConfigurationUpdaterException::FAILED_TO_DELETE_OLD_ISSUERS, $paymentMethodData['id']);
		}

		if (isset($paymentMethodData['issuers'])) {
			$paymentMethodIssuer = new MolPaymentMethodIssuer();
			$paymentMethodIssuer->issuers_json = json_encode($paymentMethodData['issuers']);
			$paymentMethodIssuer->id_payment_method = $paymentMethod->id;
			try {
				$this->entityManager->flush($paymentMethodIssuer);
			} catch (Exception $e) {
				throw new PaymentMethodConfigurationUpdaterException('Failed to update payment method configuration. Payment methods (%s) issuers unable to be saved', PaymentMethodConfigurationUpdaterException::FAILED_TO_SAVE_ISSUERS, $paymentMethodData['id']);
			}
		}

		$this->countryRepository->updatePaymentMethodCountries(
			$paymentMethodData['id'],
			$this->toolsAdapter->getValue(Config::MOLLIE_METHOD_CERTAIN_COUNTRIES . $paymentMethodData['id'])
		);

		$this->countryRepository->updatePaymentMethodExcludedCountries(
			$paymentMethodData['id'],
			$this->toolsAdapter->getValue(Config::MOLLIE_METHOD_EXCLUDE_CERTAIN_COUNTRIES . $paymentMethodData['id'])
		);

		return $paymentMethodData['id'];
	}
}
