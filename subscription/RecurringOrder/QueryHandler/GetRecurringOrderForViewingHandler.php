<?php declare(strict_types=1);

namespace Mollie\Subscription\RecurringOrder\QueryHandler;

use Mollie\Subscription\RecurringOrder\Query\GetRecurringOrderForViewing;
use Mollie\Subscription\RecurringOrder\QueryResult\ViewableRecurringOrder;
use MolRecurringOrder;
use MolRecurringOrdersProduct;

class GetRecurringOrderForViewingHandler implements GetRecurringOrderForViewingHandlerInterface
{
    public function handle(GetRecurringOrderForViewing $query): ViewableRecurringOrder
    {
        $recurringOrder = new MolRecurringOrder($query->getRecurringOrderId());
        $recurringOrdersProduct = new MolRecurringOrdersProduct($recurringOrder->id_mol_recurring_orders_product);
        return new ViewableRecurringOrder(
            $recurringOrder->id,
            $recurringOrder->mollie_subscription_id,
            $recurringOrder->mollie_customer_id,
            $recurringOrder->description,
            $recurringOrder->status,
            (float) $recurringOrdersProduct->amount,
            (int) $recurringOrdersProduct->id_currency,
            $recurringOrder->payment_method
        );
    }
}
