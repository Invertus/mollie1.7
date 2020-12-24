<?php

namespace Mollie\Verification\Form;

use Mollie\Exception\FormSettingVerificationException;

interface FormSettingVerification
{
    /**
     * @throws FormSettingVerificationException
     * @return bool
     */
    public function verify();
}