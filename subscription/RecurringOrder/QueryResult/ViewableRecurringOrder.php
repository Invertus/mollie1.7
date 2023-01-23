<?php declare(strict_types=1);

namespace Mollie\Subscription\RecurringOrder\QueryResult;

class ViewableRecurringOrder
{
    /** @var int */
    private $recurringOrderId;
    /** @var string */
    private $subscriptionId;
    /** @var string */
    private $customerId;
    /** @var string */
    private $description;
    /** @var string */
    private $status;
    /** @var float */
    private $amount;
    /** @var int */
    private $idCurrency;
    /** @var string */
    private $paymentMethod;

    public function __construct(
        int $recurringOrderId,
        string $subscriptionId,
        string $customerId,
        string $description,
        string $status,
        float $amount,
        int $idCurrency,
        string $paymentMethod
    )
    {
        $this->recurringOrderId = $recurringOrderId;
        $this->subscriptionId = $subscriptionId;
        $this->customerId = $customerId;
        $this->description = $description;
        $this->status = $status;
        $this->amount = $amount;
        $this->idCurrency = $idCurrency;
        $this->paymentMethod = $paymentMethod;
    }

    public function getRecurringOrderId(): int
    {
        return $this->recurringOrderId;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getIdCurrency(): int
    {
        return $this->idCurrency;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
}
