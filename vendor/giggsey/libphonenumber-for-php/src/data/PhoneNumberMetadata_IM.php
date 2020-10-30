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
return array('generalDesc' => array('NationalNumberPattern' => '1624\\d{6}|(?:[3578]\\d|90)\\d{8}', 'PossibleLength' => array(0 => 10), 'PossibleLengthLocalOnly' => array(0 => 6)), 'fixedLine' => array('NationalNumberPattern' => '1624[5-8]\\d{5}', 'ExampleNumber' => '1624756789', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array(0 => 6)), 'mobile' => array('NationalNumberPattern' => '76245[06]\\d{4}|7(?:4576|[59]24\\d|624[0-4689])\\d{5}', 'ExampleNumber' => '7924123456', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'tollFree' => array('NationalNumberPattern' => '808162\\d{4}', 'ExampleNumber' => '8081624567', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'premiumRate' => array('NationalNumberPattern' => '8(?:440[49]06|72299\\d)\\d{3}|(?:8(?:45|70)|90[0167])624\\d{4}', 'ExampleNumber' => '9016247890', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'sharedCost' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'personalNumber' => array('NationalNumberPattern' => '70\\d{8}', 'ExampleNumber' => '7012345678', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'voip' => array('NationalNumberPattern' => '56\\d{8}', 'ExampleNumber' => '5612345678', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'pager' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'uan' => array('NationalNumberPattern' => '3440[49]06\\d{3}|(?:3(?:08162|3\\d{4}|45624|7(?:0624|2299))|55\\d{4})\\d{4}', 'ExampleNumber' => '5512345678', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'voicemail' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'noInternationalDialling' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'id' => 'IM', 'countryCode' => 44, 'internationalPrefix' => '00', 'nationalPrefix' => '0', 'nationalPrefixForParsing' => '0|([5-8]\\d{5})$', 'nationalPrefixTransformRule' => '1624$1', 'sameMobileAndFixedLinePattern' => \false, 'numberFormat' => array(), 'intlNumberFormat' => array(), 'mainCountryForCode' => \false, 'leadingDigits' => '74576|(?:16|7[56])24', 'leadingZeroPossible' => \false, 'mobileNumberPortableRegion' => \false);
