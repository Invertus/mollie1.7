<?php

namespace Mollie\Utility;

class PhoneNumberUtility
{
    /**
     * Simple and naive implementation of checking if phone number is international. This does only check if number
     * is numeric and has + sign in front of it.
     *
     * @param $phoneNumber
     *
     * @return bool
     */
    public static function isInternationalPhoneNumber($phoneNumber)
    {
        $hasPlusPrefix = strpos($phoneNumber, '+') === 0;

        if (!$hasPlusPrefix) {
            return false;
        }

        $onlyPhoneNumber = str_replace('+', '', $phoneNumber);

        return is_numeric($onlyPhoneNumber);
    }
}
