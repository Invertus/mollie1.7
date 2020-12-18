<?php

use Mollie\Config\Config;
use Mollie\Service\PaymentMethod\PaymentMethodRestrictionValidation\KlarnaPayLaterPaymentMethodRestrictionValidator;
use Mollie\Service\PaymentMethod\PaymentMethodRestrictionValidation\KlarnaSliceItPaymentMethodRestrictionValidator;
use Mollie\Tests\Unit\Tools\UnitTestCase;

class KlarnaSliceItPaymentRestrictionValidationTest extends UnitTestCase
{
    /**
     * @dataProvider getKlarnaSliceItPaymentRestrictionValidationDataProvider
     */
    public function testIsValid($context, $paymentMethodCountryProvider, $expectedResult)
    {
        $klarnaPayLaterValidation = new KlarnaSliceItPaymentMethodRestrictionValidator(
            $context,
            $paymentMethodCountryProvider
        );

        $isValid = $klarnaPayLaterValidation->isValid(
            $this->mockPaymentMethod(Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID)
        );
        $this->assertEquals($expectedResult, $isValid);
    }

    /**
     * @dataProvider getKlarnaSliceItPaymentRestrictionSupportedDataProvider
     */
    public function testIsSupported($context, $paymentMethodCountryProvider, $paymentName, $expectedResult)
    {
        $klarnaValidation = new KlarnaSliceItPaymentMethodRestrictionValidator(
            $context,
            $paymentMethodCountryProvider
        );
        $this->assertEquals($expectedResult, $klarnaValidation->supports($this->mockPaymentMethod($paymentName)));
    }

    public function getKlarnaSliceItPaymentRestrictionValidationDataProvider()
    {
        return [
            [
                'context' => $this->mockContext('AT', 'AUD'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'expectedResult' => true,
            ],
            [
                'context' => $this->mockContext('DK', 'CAD'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'expectedResult' => true,
            ],
            [
                'context' => $this->mockContext('DE', 'EUR'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'expectedResult' => true,
            ],
            [
                'context' => $this->mockContext('LT', 'USD'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'expectedResult' => false,
            ],
            [
                'context' => $this->mockContext('DE', 'LT'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'expectedResult' => false,
            ],
        ];
    }

    public function getKlarnaSliceItPaymentRestrictionSupportedDataProvider()
    {
        return [
            [
                'context' => $this->mockContext('AT', 'AUD'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'paymentName' => Config::MOLLIE_KLARNA_SLICE_IT_METHOD_ID,
                'expectedResult' => true,
            ],
            [
                'context' => $this->mockContext('AT', 'AUD'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'paymentName' => Config::APPLEPAY,
                'expectedResult' => false,
            ],
        ];
    }
}
