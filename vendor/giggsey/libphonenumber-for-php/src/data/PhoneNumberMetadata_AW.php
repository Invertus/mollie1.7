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
return array('generalDesc' => array('NationalNumberPattern' => '(?:[25-79]\\d\\d|800)\\d{4}', 'PossibleLength' => array(0 => 7), 'PossibleLengthLocalOnly' => array()), 'fixedLine' => array('NationalNumberPattern' => '5(?:2\\d|8[1-9])\\d{4}', 'ExampleNumber' => '5212345', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'mobile' => array('NationalNumberPattern' => '(?:290|5[69]\\d|6(?:[03]0|22|4[0-2]|[69]\\d)|7(?:[34]\\d|7[07])|9(?:6[45]|9[4-8]))\\d{4}', 'ExampleNumber' => '5601234', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'tollFree' => array('NationalNumberPattern' => '800\\d{4}', 'ExampleNumber' => '8001234', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'premiumRate' => array('NationalNumberPattern' => '900\\d{4}', 'ExampleNumber' => '9001234', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'sharedCost' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'personalNumber' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'voip' => array('NationalNumberPattern' => '(?:28\\d|501)\\d{4}', 'ExampleNumber' => '5011234', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'pager' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'uan' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'voicemail' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'noInternationalDialling' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'id' => 'AW', 'countryCode' => 297, 'internationalPrefix' => '00', 'sameMobileAndFixedLinePattern' => \false, 'numberFormat' => array(0 => array('pattern' => '(\\d{3})(\\d{4})', 'format' => '$1 $2', 'leadingDigitsPatterns' => array(0 => '[25-9]'), 'nationalPrefixFormattingRule' => '', 'domesticCarrierCodeFormattingRule' => '', 'nationalPrefixOptionalWhenFormatting' => \false)), 'intlNumberFormat' => array(), 'mainCountryForCode' => \false, 'leadingZeroPossible' => \false, 'mobileNumberPortableRegion' => \false);
