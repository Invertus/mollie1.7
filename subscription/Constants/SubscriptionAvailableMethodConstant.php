<?php

declare(strict_types=1);

namespace Mollie\Subscription\Constants;

class SubscriptionAvailableMethodConstant
{
    public const CREDIT_CARD = 'creditcard';
    public const DIRECT_DEBIT = 'directdebit';
    public const PAYPAL = 'paypal';
    public const NULL = 'null';

    public const AVAILABLE_METHODS = [
        self::CREDIT_CARD => self::CREDIT_CARD,
        self::DIRECT_DEBIT => self::DIRECT_DEBIT,
        self::PAYPAL => self::PAYPAL,
    ];
}
