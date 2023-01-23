<?php

class MolRecurringOrdersProduct extends ObjectModel
{
    /** @var int */
    public $id_product;

    /** @var int */
    public $id_product_attribute;

    /** @var string */
    public $quantity;

    /** @var string */
    public $amount;

    /** @var string */
    public $id_currency;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_update;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'mol_recurring_orders_product',
        'primary' => 'id_mol_recurring_orders_product',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'quantity' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'amount' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'id_currency' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_update' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];
}
