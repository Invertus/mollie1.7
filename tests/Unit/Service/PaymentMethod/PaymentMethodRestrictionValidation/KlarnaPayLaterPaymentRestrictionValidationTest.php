<?php


use Mollie\Config\Config;
use Mollie\Service\PaymentMethod\PaymentMethodRestrictionValidation\KlarnaPayLaterPaymentMethodRestrictionValidator;
use Mollie\Tests\Unit\Tools\UnitTestCase;

class KlarnaPayLaterPaymentRestrictionValidationTest extends UnitTestCase
{
    /**
     * @dataProvider getKlarnaPayLaterPaymentRestrictionValidationDataProvider
     */
    public function testIsValid($context, $paymentMethodCountryProvider, $expectedResult)
    {
        $klarnaPayLaterValidation = new KlarnaPayLaterPaymentMethodRestrictionValidator(
            $context,
            $paymentMethodCountryProvider
        );

        $isValid = $klarnaPayLaterValidation->isValid(
            $this->mockPaymentMethod(Config::MOLLIE_KLARNA_PAY_LATER_METHOD_ID)
        );
        $this->assertEquals($expectedResult, $isValid);
    }

    /**
     * @dataProvider getKlarnaPayLaterPaymentRestrictionSupportedDataProvider
     */
    public function testIsSupported($context, $paymentMethodCountryProvider, $paymentName, $expectedResult)
    {
        $klarnaValidation = new KlarnaPayLaterPaymentMethodRestrictionValidator(
            $context,
            $paymentMethodCountryProvider
        );
        $this->assertEquals($expectedResult, $klarnaValidation->supports($this->mockPaymentMethod($paymentName)));
    }

    public function getKlarnaPayLaterPaymentRestrictionValidationDataProvider()
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

    public function getKlarnaPayLaterPaymentRestrictionSupportedDataProvider()
    {
        return [
            [
                'context' => $this->mockContext('AT', 'AUD'),
                'paymentMethodCountryProvider' => $this->mockPaymentMethodCountryProvider([
                    'nl', 'de', 'at', 'fi'
                ]),
                'paymentName' => Config::MOLLIE_KLARNA_PAY_LATER_METHOD_ID,
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
