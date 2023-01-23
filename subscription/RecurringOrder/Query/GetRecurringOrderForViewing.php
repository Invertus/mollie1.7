<?php declare(strict_types=1);

namespace Mollie\Subscription\RecurringOrder\Query;

class GetRecurringOrderForViewing
{
    /** @var int */
    private $recurringOrderId;

    public function __construct(int $recurringOrderId)
    {
        $this->recurringOrderId = $recurringOrderId;
    }

    public function getRecurringOrderId(): int
    {
        return $this->recurringOrderId;
    }
}
