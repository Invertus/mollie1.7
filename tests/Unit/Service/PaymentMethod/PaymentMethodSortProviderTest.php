<?php

use Mollie\Service\PaymentMethod\PaymentMethodSortProvider;
use PHPUnit\Framework\TestCase;

class FakePaymentMethod extends MolPaymentMethod {

    public $name;
    public $position;

    public function __construct($name, $position)
    {
        $this->name = $name;
        $this->position = $position;
    }
}

class PaymentMethodSortProviderTest extends TestCase
{
    public function testItSortsPaymentMethodsAscendingWay()
    {
        $p1 = new FakePaymentMethod('extraSafe', 0);
        $p2 = new FakePaymentMethod('superSafe', 2);
        $p3 = new FakePaymentMethod('paySafe', 4);

        $paymentMethods = [
            $p3,
            $p1,
            $p2,
        ];

        $sort = new PaymentMethodSortProvider();
        $sorted = $sort->getSortedInAscendingWay($paymentMethods);

        $this->assertEquals(
            [
                $p1,
                $p2,
                $p3,
            ],
            $sorted
        );
    }
}
