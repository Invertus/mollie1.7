<?php

namespace MolliePrefix;

/**
 * This file has been @generated by a phing task by {@link BuildMetadataPHPFromXml}.
 * See [README.md](README.md#generating-data) for more information.
 *
 * Pull requests changing data in these files will not be accepted. See the
 * [FAQ in the README](README.md#problems-with-invalid-numbers] on how to make
 * metadata changes.
 *
 * Do not modify this file directly!
 */
return array('generalDesc' => array('NationalNumberPattern' => '[149]\\d\\d(?:\\d{2,3})?', 'PossibleLength' => array(0 => 3, 1 => 5, 2 => 6), 'PossibleLengthLocalOnly' => array()), 'tollFree' => array('NationalNumberPattern' => '116\\d{3}|911', 'ExampleNumber' => '911', 'PossibleLength' => array(0 => 3, 1 => 6), 'PossibleLengthLocalOnly' => array()), 'premiumRate' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'emergency' => array('NationalNumberPattern' => '91[13]', 'ExampleNumber' => '911', 'PossibleLength' => array(0 => 3), 'PossibleLengthLocalOnly' => array()), 'shortCode' => array('NationalNumberPattern' => '1(?:1(?:2|6111)|2[136-8]|3[0-6]|9[05])|40404|9(?:1\\d|29)', 'ExampleNumber' => '112', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'standardRate' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'carrierSpecific' => array('NationalNumberPattern' => 'MolliePrefix\\404\\d\\d', 'ExampleNumber' => '40400', 'PossibleLength' => array(0 => 5), 'PossibleLengthLocalOnly' => array()), 'smsServices' => array('NationalNumberPattern' => 'MolliePrefix\\404\\d\\d', 'ExampleNumber' => '40400', 'PossibleLength' => array(0 => 5), 'PossibleLengthLocalOnly' => array()), 'id' => 'SV', 'countryCode' => 0, 'internationalPrefix' => '', 'sameMobileAndFixedLinePattern' => \false, 'numberFormat' => array(), 'intlNumberFormat' => array(), 'mainCountryForCode' => \false, 'leadingZeroPossible' => \false, 'mobileNumberPortableRegion' => \false);
