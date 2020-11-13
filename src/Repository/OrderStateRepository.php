<?php

namespace Mollie\Repository;

use Db;

class OrderStateRepository
{
    public function deleteMollieStatuses($name) {
        $sql = 'UPDATE '. _DB_PREFIX_ . 'order_state SET deleted = 1 WHERE module_name = "' . psql($name) . '"';

        return Db::getInstance()->execute($sql);
    }
}
