<?php

namespace Mollie\Tests\Unit\Tools;

use Mollie\Adapter\LegacyContext;
use Mollie\Provider\OrderTotalRestrictionProvider;
use Mollie\Provider\PaymentMethodCountryProvider;
use Mollie\Provider\PaymentMethodCurrencyProvider;
use Mollie\Provider\PaymentMethodCurrencyProviderInterface;
use Mollie\Service\OrderTotalService;
use MolPaymentMethod;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    public function mockContext($countryCode, $currencyCode)
    {
        $contextMock = $this->getMockBuilder(LegacyContext::class)
            ->getMock();

        $contextMock
            ->method('getCountryIsoCode')
            ->willReturn($countryCode)
        ;

        $contextMock
            ->method('getCurrencyIsoCode')
            ->willReturn($currencyCode)
        ;

        $contextMock
            ->method('getCurrencyId')
            ->willReturn(1)
        ;

        $contextMock
            ->method('getCountryId')
            ->willReturn(1)
        ;

        return $contextMock;
    }

    public function mockPaymentMethod($paymentName)
    {
        $paymentMethod = $this->getMockBuilder(MolPaymentMethod::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentMethod->id_method = $paymentName;

        return $paymentMethod;
    }

    public function mockPaymentMethodCountryProvider($availableCountries)
    {
        $paymentMethodCountryProvider = $this->getMockBuilder(PaymentMethodCountryProvider::class)
            ->getMock();

        $paymentMethodCountryProvider->method('provideAvailableCountriesByPaymentMethod')
            ->willReturn($availableCountries)
        ;

        return $paymentMethodCountryProvider;
    }

    public function mockPaymentMethodCurrencyProvider($availableCurrencies)
    {
        $paymentMethodCountryProvider = $this->getMockBuilder(PaymentMethodCurrencyProvider::class)
            ->getMock();

        $paymentMethodCountryProvider->method('provideAvailableCurrenciesByPaymentMethod')
            ->willReturn($availableCurrencies)
        ;

        return $paymentMethodCountryProvider;
    }

    public function mockOrderTotalService($isOrderTotalHigherThanMaximum, $isOrderTotalLowerThanMinimum)
    {
        $orderTotalService = $this->getMockBuilder(OrderTotalService::class)
            ->getMock();

        $orderTotalService->method('isOrderTotalLowerThanMinimumAllowed')
            ->willReturn($isOrderTotalLowerThanMinimum)
        ;

        $orderTotalService->method('isOrderTotalHigherThanMinimumAllowed')
            ->willReturn($isOrderTotalHigherThanMaximum)
        ;

        return $orderTotalService;
    }

    public function mockOrderTotalRestrictionProvider($minimumValue, $maximumValue)
    {
        $orderTotalRestrictionProvider = $this->getMockBuilder(OrderTotalRestrictionProvider::class)
            ->getMock();

        $orderTotalRestrictionProvider->method('provideOrderTotalMinimumRestriction')
            ->willReturn($minimumValue)
        ;

        $orderTotalRestrictionProvider->method('provideOrderTotalMaximumRestriction')
            ->willReturn($maximumValue)
        ;

        return $orderTotalRestrictionProvider;
    }
}
