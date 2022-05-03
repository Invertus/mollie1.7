<?php
/**
 * Mollie       https://www.mollie.nl
 *
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 * @license     https://github.com/mollie/PrestaShop/blob/master/LICENSE.md
 *
 * @see        https://github.com/mollie/PrestaShop
 * @codingStandardsIgnoreStart
 */

namespace Mollie\Service;

class RepeatOrderLinkFactory
{
    public function getLink()
    {
        $globalContext = \Context::getContext();

        return $globalContext->link->getPageLink(
            'cart',
            null,
            $globalContext->language->id,
            [
                'action' => 'show',
            ]
        );
    }
}
