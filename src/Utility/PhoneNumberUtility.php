<?php

namespace Mollie\Utility;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mollie\Exception\PhoneNumberException;
use Validate;

class PhoneNumberUtility
{
    /**
     * @param string $number
     * @param string $countryIsoCode
     *
     * @throws PhoneNumberException
     */
    public static function internationalizeNumber($number, $countryIsoCode)
    {
        if (!Validate::isLanguageIsoCode($countryIsoCode)) {
            throw new PhoneNumberException(
                'Invalid country code. Expected to match format "/^[a-zA-Z]{2,3}$/"',
                NumberParseException::INVALID_COUNTRY_CODE
            );
        }

        $normalizedNumber = self::normalizeNumber($number, $countryIsoCode);

        $phoneFormatter = self::getInstance();

        return $phoneFormatter->format($normalizedNumber, PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * @param $number
     * @param $countryIsoCode
     * @return PhoneNumber
     *
     * @throws PhoneNumberException
     */
    private static function normalizeNumber($number, $countryIsoCode)
    {
        $phoneFormatter = self::getInstance();

        try {
            return $phoneFormatter->parse($number, $countryIsoCode);
        } catch (NumberParseException $exception) {
            throw new PhoneNumberException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private static function getInstance()
    {
        return PhoneNumberUtil::getInstance();
    }
}
