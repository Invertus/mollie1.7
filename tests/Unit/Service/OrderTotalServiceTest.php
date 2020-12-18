<?php

use Mollie\Config\Config;
use Mollie\Service\OrderTotalService;
use Mollie\Tests\Unit\Tools\UnitTestCase;

class OrderTotalServiceTest extends UnitTestCase
{
    /**
     * @dataProvider testIsOrderTotalLowerThanMinimumAllowedDataProvider
     */
    public function testIsOrderTotalLowerThanMinimumAllowed($paymentMethod, $orderTotal, $minimumValue, $maximumValue, $expectedResult)
    {
        $orderTotalService = new OrderTotalService(
            $this->mockContext('AT', 'AUD'),
            $this->mockOrderTotalRestrictionProvider($minimumValue, $maximumValue)
        );

        $isAllowed = $orderTotalService->isOrderTotalHigherThanMaximumAllowed($paymentMethod, $orderTotal);
        $this->assertEquals($expectedResult, $isAllowed);
    }

    public function testIsOrderTotalLowerThanMinimumAllowedDataProvider()
    {
        return [
            'Not lower than minimum allowed' => [
                'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID),
                'orderTotal' => 100.01,
                'minimumAmount' => 10.00,
                'maximumAmount' => 200.00,
                'expectedValue' => false
            ],
            'Lower than minimum allowed' => [
                'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID),
                'orderTotal' => 5.01,
                'minimumAmount' => 10.00,
                'maximumAmount' => 200.00,
                'expectedValue' => true
            ],
        ];
    }

    /**
     * @dataProvider testIsOrderTotalHigherThanMaximumAllowedDataProvider
     */
    public function testIsOrderTotalHigherThanMaximumAllowed($paymentMethod, $orderTotal, $minimumValue, $maximumValue, $expectedResult)
    {
        $orderTotalService = new OrderTotalService(
            $this->mockContext('AT', 'AUD'),
            $this->mockOrderTotalRestrictionProvider($minimumValue, $maximumValue)
        );

        $isAllowed = $orderTotalService->isOrderTotalHigherThanMaximumAllowed($paymentMethod, $orderTotal);
        $this->assertEquals($expectedResult, $isAllowed);
    }

    public function testIsOrderTotalHigherThanMaximumAllowedDataProvider()
    {
        return [
            'Not higher than maximum allowed' => [
                'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID),
                'orderTotal' => 100.01,
                'minimumAmount' => 10.00,
                'maximumAmount' => 200.00,
                'expectedValue' => false
            ],
            'No maximum amount is specified' => [
                'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID),
                'orderTotal' => 200.01,
                'minimumAmount' => 10.00,
                'maximumAmount' => 0,
                'expectedValue' => false
            ],
            'Higher than maximum allowed' => [
                'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID),
                'orderTotal' => 200.01,
                'minimumAmount' => 10.00,
                'maximumAmount' => 200.00,
                'expectedValue' => true
            ],
        ];
    }
}
