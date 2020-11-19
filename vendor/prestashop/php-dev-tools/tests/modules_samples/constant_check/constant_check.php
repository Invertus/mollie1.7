<?php

namespace test;

class Constant_Check extends \test\Module
{
    public function __construct()
    {
        $this->name = 'constants_check';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'PrestaShopCorp';
        $this->need_instance = 0;
        // This constant must trigger an error on PHPStan below PS 1.6.0.11
        $value = \_PS_PRICE_COMPUTE_PRECISION_;
    }
}
\class_alias('test\\Constant_Check', 'test\\Constant_Check', \false);
