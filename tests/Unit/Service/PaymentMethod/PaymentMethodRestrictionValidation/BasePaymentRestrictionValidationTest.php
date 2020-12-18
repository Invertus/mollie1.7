<?php

use Invertus\SaferPay\Config\SaferPayConfig;
use Mollie\Config\Config;
use Mollie\Service\PaymentMethod\PaymentMethodRestrictionValidation\BasePaymentMethodRestrictionValidator;
use Mollie\Tests\Unit\Tools\UnitTestCase;

class BasePaymentRestrictionValidationTest extends UnitTestCase
{
    /**
     * @dataProvider getBasePaymentRestrictionValidationDataProvider
     */
    public function testIsValid(
        $paymentMethod,
        $context,
        $orderTotalService,
        $paymentMethodCurrencyProvider,
        $expectedResult
    ) {
        $basePaymentRestrictionValidation = new BasePaymentMethodRestrictionValidator(
            $context,
            $orderTotalService,
            $paymentMethodCurrencyProvider
        );
        $this->assertEquals($expectedResult, $basePaymentRestrictionValidation->isValid($paymentMethod));
    }

    public function getBasePaymentRestrictionValidationDataProvider()
    {
        return [
            [
                'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID),
                'context' => $this->mockContext('AT', 'AUD'),
                'orderTotalService' => $this->mockOrderTotalService(true, false),
                'paymentMethodCurrencyProvider' => $this->mockPaymentMethodCurrencyProvider([
                    'aud', 'bgn', 'cad', 'chf', 'czk', 'dkk', 'eur', 'gbp', 'hkd', 'hrk', 'huf', 'ils', 'isk', 'jpy', 'pln', 'ron', 'sek', 'usd', 'rub'
                ]),
                'expectedResult' => true
            ]
        ];
    }
}
