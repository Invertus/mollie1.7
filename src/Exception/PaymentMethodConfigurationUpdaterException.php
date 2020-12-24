<?php
/**
 * Mollie       https://www.mollie.nl
 *
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 *
 * @see         https://github.com/mollie/PrestaShop
 *
 * @license     https://github.com/mollie/PrestaShop/blob/master/LICENSE.md
 * @codingStandardsIgnoreStart
 */

namespace Mollie\Exception;

use Exception;

class PaymentMethodConfigurationUpdaterException extends Exception
{
	const NO_PAYMENT_METHOD_DATA_PROVIDED = 1;

	const FAILED_TO_SAVE_PAYMENT_METHOD = 2;

	const FAILED_TO_DELETE_OLD_ISSUERS = 3;

	const FAILED_TO_SAVE_ISSUERS = 4;

	/**
	 * @var string
	 */
	private $paymentMethodName;

	public function __construct($message, $code, $paymentMethodName, Exception $previous = null)
	{
		$this->paymentMethodName = $paymentMethodName;

		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return string
	 */
	public function getPaymentMethodName()
	{
		return $this->paymentMethodName;
	}
}
