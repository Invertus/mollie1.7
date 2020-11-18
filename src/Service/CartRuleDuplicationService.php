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
 */

namespace Mollie\Service;

use Cart;
use CartRule;
use Context;
use Db;
use Mollie\Config\Config;
use Order;

class CartRuleDuplicationService
{
    /**
     * @param array $cartRules
     *
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function restoreCartRules($cartRules = [])
    {
        if (empty($cartRules)) {
            return true;
        }
        $context = Context::getContext();

        foreach ($cartRules as $cartRuleContent) {
            $cartRule = new CartRule($cartRuleContent['id_cart_rule']);
            if ($cartRule->checkValidity($context, false, false)) {
                $context->cart->addCartRule($cartRule->id);
            }
        }

        return true;
    }

    /**
     * To duplicate cart rules quantities must be reset to pass validation (Cart rules for new cart are created before removing from previous cart by PS)
     *
     * @param Cart $cart
     * @param string $backtraceLocation
     * @param array $cartRules
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function resetQuantities($cart, $backtraceLocation, $cartRules = [])
    {
        if (empty($cartRules)) {
            return;
        }
        $order = Order::getByCartId($cart->id);

        foreach ($cartRules as $cartRuleContent) {
            $cartRule = new CartRule($cartRuleContent['id_cart_rule']);
            /** To fix issue with adding quantity twice */
            if ($backtraceLocation == Config::RESTORE_CART_BACKTRACE_RETURN_CONTROLLER) {
                $this->updateAvailableCartRuleQuantity($cartRule);
            }
            $this->updateCustomerUsedCartRuleQuantity($order, $cartRule);
        }
    }

    /**
     * @param $order
     * @param $cartRule
     *
     * @return bool
     */
    private function updateCustomerUsedCartRuleQuantity($order, $cartRule)
    {
        return Db::getInstance()->delete(
            'order_cart_rule',
            'id_order= ' . (int) $order->id . ' AND id_cart_rule= ' . (int) $cartRule->id,
            1
        );
    }

    /**
     * @param CartRule $cartRule
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateAvailableCartRuleQuantity($cartRule)
    {
        $cartRule->quantity = $cartRule->quantity + 1;
        $cartRule->update();
    }
}