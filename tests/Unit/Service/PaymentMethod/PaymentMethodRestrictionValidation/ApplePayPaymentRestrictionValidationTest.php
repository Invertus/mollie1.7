<?php

use Mollie\Config\Config;
use Mollie\Service\PaymentMethod\PaymentMethodRestrictionValidation\ApplePayPaymentMethodRestrictionValidator;
use Mollie\Tests\Unit\Tools\UnitTestCase;

class ApplePayPaymentRestrictionValidationTest extends UnitTestCase
{
    /**
     * @dataProvider getApplePayPaymentRestrictionSupportedDataProvider
     */
    public function testIsSupported($paymentName, $expectedResult)
    {
        $applePayValidation = new ApplePayPaymentMethodRestrictionValidator($this->mockContext('AT', 'AUD'));
        $this->assertEquals($expectedResult, $applePayValidation->supports($paymentName));
    }

    public function getApplePayPaymentRestrictionSupportedDataProvider()
    {
        return [
            [
                'paymentMethod' => $this->mockPaymentMethod(Config::APPLEPAY),
                'expectedResult' => true,
            ],
            [
                'paymentName' => $this->mockPaymentMethod(Config::MOLLIE_KLARNA_PAY_LATER_METHOD_ID),
                'expectedResult' => false,
            ],
        ];
    }
}
