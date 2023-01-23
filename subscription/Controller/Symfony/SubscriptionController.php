<?php

declare(strict_types=1);

namespace Mollie\Subscription\Controller\Symfony;

use Exception;
use Mollie\Api\Types\SubscriptionStatus;
use Mollie\Subscription\Api\SubscriptionApi;
use Mollie\Subscription\Exception\SubscriptionApiException;
use Mollie\Subscription\Factory\CancelSubscriptionDataFactory;
use Mollie\Subscription\Factory\GetSubscriptionDataFactory;
use Mollie\Subscription\Filters\SubscriptionFilters;
use Mollie\Subscription\Handler\SubscriptionCancellationHandler;
use Mollie\Subscription\RecurringOrder\Query\GetRecurringOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\ViewableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends AbstractSymfonyController
{
    private const FILE_NAME = 'SubscriptionController';

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param SubscriptionFilters $filters
     *
     * @return Response
     */
    public function indexAction(SubscriptionFilters $filters, Request $request)
    {
        /** @var GridFactoryInterface $currencyGridFactory */
        $currencyGridFactory = $this->leagueContainer->getService('subscription_grid_factory');
        $currencyGrid = $currencyGridFactory->getGrid($filters);

        return $this->render('@Modules/mollie/views/templates/admin/Subscription/index.html.twig', [
            'currencyGrid' => $this->presentGrid($currencyGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Show customer edit form & handle processing of it.
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @param int $subscriptionId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $subscriptionId, Request $request)
    {
        try {
            /** @var ViewableCustomer $customerInformation */
            $recurringOrderInformation = $this->getQueryBus()->handle(new GetRecurringOrderForViewing((int) $subscriptionId));
            $recurringOrderForm = $this->get('recurring_order_form_builder')
                ->getFormFor((int) $subscriptionId);
            $recurringOrderForm->handleRequest($request);
            $RecurringOrderFormHandler = $this->get('recurring_order_form_handler');
            $result = $RecurringOrderFormHandler->handleFor((int) $subscriptionId, $recurringOrderForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_customers_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            if ($e instanceof CustomerNotFoundException) {
                return $this->redirectToRoute('admin_customers_index');
            }
        }

        return $this->render('@Modules/mollie/views/templates/admin/Subscription/edit.html.twig', [
            'recurringOrderForm' => $recurringOrderForm->createView(),
            'recurringOrderInformation' => $recurringOrderInformation,
        ]);
    }


    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_subscription_index")
     *
     * @param int $subscriptionId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $subscriptionId): RedirectResponse
    {
        /** @var SubscriptionApi $subscriptionApi */
        $subscriptionApi = $this->leagueContainer->getService(SubscriptionApi::class);

        /** @var SubscriptionCancellationHandler $orderCancellationHandler */
        $orderCancellationHandler = $this->leagueContainer->getService(SubscriptionCancellationHandler::class);

        /** @var CancelSubscriptionDataFactory $cancelSubscriptionDataFactory */
        $cancelSubscriptionDataFactory = $this->leagueContainer->getService(CancelSubscriptionDataFactory::class);

        /** @var GetSubscriptionDataFactory $getSubscriptionDataFactory */
        $getSubscriptionDataFactory = $this->leagueContainer->getService(GetSubscriptionDataFactory::class);

        try {
            $cancelSubscriptionData = $cancelSubscriptionDataFactory->build($subscriptionId);
            $subscription = $subscriptionApi->cancelSubscription($cancelSubscriptionData);
        } catch (SubscriptionApiException $e) {
            // if subscription cancel fails we check if its already canceled and if its then we update it to canceled
            $getSubscriptionData = $getSubscriptionDataFactory->build($subscriptionId);
            try {
                $subscription = $subscriptionApi->getSubscription($getSubscriptionData);
            } catch (SubscriptionApiException $e) {
                $this->addFlash('error', $this->getErrorMessage($e));

                return $this->redirectToRoute('admin_subscription_index');
            }

            if ($subscription->status !== SubscriptionStatus::STATUS_CANCELED) {
                $this->addFlash('error', $this->getErrorMessage($e));

                return $this->redirectToRoute('admin_subscription_index');
            }
        }

        $this->addFlash(
            'success',
            $this->module->l('Successfully canceled', self::FILE_NAME)
        );

        $orderCancellationHandler->handle($subscriptionId, $subscription->status, $subscription->canceledAt);

        return $this->redirectToRoute('admin_subscription_index');
    }

    private function getErrorMessage(Exception $e): string
    {
        $errors = [];

        if ($e instanceof SubscriptionApiException) {
            $errors[SubscriptionApiException::class] = [
                SubscriptionApiException::CANCELLATION_FAILED => $this->module->l('Failed to cancel subscription', self::FILE_NAME),
            ];
        }

        return $this->getErrorMessageForException($e, $errors);
    }
}
