<?php

namespace Mollie\Service\Form;

interface FormSaver
{
    /**
     * @return bool
     */
    public function saveConfiguration();
}