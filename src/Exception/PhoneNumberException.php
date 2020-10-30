<?php

namespace Mollie\Exception;

/**
 * At the time of writing this class forwards errors from NumberParseException in libphonenumber library
 */
class PhoneNumberException extends MollieException
{
}
