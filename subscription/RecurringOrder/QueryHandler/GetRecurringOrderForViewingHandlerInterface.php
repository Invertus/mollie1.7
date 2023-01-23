<?php

namespace Mollie\Subscription\RecurringOrder\QueryHandler;

use Mollie\Subscription\RecurringOrder\Query\GetRecurringOrderForViewing;
use Mollie\Subscription\RecurringOrder\QueryResult\ViewableRecurringOrder;

interface GetRecurringOrderForViewingHandlerInterface
{
    public function handle(GetRecurringOrderForViewing $query): ViewableRecurringOrder;
}
