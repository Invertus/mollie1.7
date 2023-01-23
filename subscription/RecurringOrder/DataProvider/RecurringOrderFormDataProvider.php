<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Mollie\Subscription\RecurringOrder\DataProvider;

use Mollie\Subscription\Constants\SubscriptionAvailableMethodConstant;
use Mollie\Subscription\RecurringOrder\Query\GetRecurringOrderForViewing;
use Mollie\Subscription\RecurringOrder\QueryResult\ViewableRecurringOrder;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * Provides data for recurring order forms
 */
class RecurringOrderFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        CommandBusInterface $queryBus
    ) {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($recurringOrderId): array
    {
        /** @var ViewableRecurringOrder $viewableRecurringOrder */
        $viewableRecurringOrder = $this->queryBus->handle(new GetRecurringOrderForViewing((int) $recurringOrderId));

        $data = [
            'recurring_order_id' => $viewableRecurringOrder->getRecurringOrderId(),
            'status' => $viewableRecurringOrder->getStatus(),
            'method' => $viewableRecurringOrder->getPaymentMethod(),
            'id_product_attribute' => $viewableRecurringOrder->getPaymentMethod(),
        ];

        return $data;
    }

    public function getDefaultData(): array
    {
        return [];
    }
}
