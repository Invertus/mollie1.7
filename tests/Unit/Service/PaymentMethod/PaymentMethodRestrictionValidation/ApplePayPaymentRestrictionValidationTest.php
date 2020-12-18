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
			'Supported' => [
				'paymentMethod' => $this->mockPaymentMethod(Config::MOLLIE_METHOD_ID_APPLE_PAY, true),
				'expectedResult' => true,
			],
			'Not supported' => [
				'paymentName' => $this->mockPaymentMethod(Config::MOLLIE_METHOD_ID_KLARNA_PAY_LATER, true),
				'expectedResult' => false,
			],
		];
	}
}
