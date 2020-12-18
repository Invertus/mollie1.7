<?php
/**
 * Copyright (c) 2012-2020, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @author     Mollie B.V. <info@mollie.nl>
 * @copyright  Mollie B.V.
 * @license    Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @category   Mollie
 * @package    Mollie
 * @link       https://www.mollie.nl
 * @codingStandardsIgnoreStart
 */

class MolPaymentMethodOrderTotalRestriction extends ObjectModel
{
    /**
     * @var int
     */
    public $id_payment_method;

    /**
     * @var int
     */
    public $currencyId;

    /**
     * @var string
     */
    public $minimalOrderTotal;

    /**
     * @var string
     */
    public $maximumOrderTotal;

    /**
     * @var array
     */
    public static $definition = array(
        'table' => 'mol_payment_method_order_total_restriction',
        'primary' => 'id_payment_method_order_total_restriction',
        'fields' => array(
            'id_payment_method_order_total_restriction' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'currencyId' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'minimalOrderTotal' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'maximumOrderTotal' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
        ),
    );

    /**
     * @param PaymentMethod $paymentMethod
     *
     * @return float
     */
    private function getMinimalOrderValue($paymentMethod)
    {
        //TODO  $result = $this->client->performHttpCall(self::REST_READ, "{$this->getResourcePath()}/{$id}" . '?currency=EUR'); change currency
        // if it's null then just do nothing
        if (!isset($paymentMethod['minimumAmount'])) {
            return 0.0; //TODO default minimum amount
        }
        $mollieMinimumOrderTotalCurrencyId = Currency::getIdByIsoCode($paymentMethod['minimumAmount']->currency);

        if (empty($mollieMinimumOrderTotalCurrencyId)) {
            return 0.0; //As there is no way to convert currency from EUR (Mollie default currency) to shops currency.
        }
        $mollieMinimumOrderTotalCurrency = Currency::getCurrency($mollieMinimumOrderTotalCurrencyId);

        return Tools::convertPrice($paymentMethod['minimumAmount']->value, $mollieMinimumOrderTotalCurrency, false);
    }

    /**
     * @param PaymentMethod $paymentMethod
     *
     * @return float
     */
    private function getMaximumOrderValue($paymentMethod)
    {
        if (!isset($paymentMethod['maximumAmount'])) {
            return 0.0; //TODO check if 0 then do not check maximum
        }
        $mollieMaximumOrderTotalCurrencyId = Currency::getIdByIsoCode($paymentMethod['maximumAmount']->currency);

        if (empty($mollieMaximumOrderTotalCurrencyId)) {
            return 0.0; //As there is no way to convert currency from EUR (Mollie default currency) to shops currency.
        }
        $mollieMaximumOrderTotalCurrency = Currency::getCurrency($mollieMaximumOrderTotalCurrencyId);

        return Tools::convertPrice($paymentMethod['maximumAmount']->value, $mollieMaximumOrderTotalCurrency, false);
    }
}