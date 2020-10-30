<?php

namespace Mollie\Service\PaymentMethod;

use MolPaymentMethod;

final class PaymentMethodSortProvider implements PaymentMethodSortProviderInterface
{
    public function getSortedInAscendingWay(array $paymentMethods): array
    {
        usort($paymentMethods, function (MolPaymentMethod $a, MolPaymentMethod $b) {
            if ($a->position === $b->position) {
                return 0;
            }

            return ($a->position < $b->position) ? -1 : 1;
        });

        return $paymentMethods;
    }
}
