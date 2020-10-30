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
return array('generalDesc' => array('NationalNumberPattern' => '(?:[378]\\d{4}|93330)\\d{4}', 'PossibleLength' => array(0 => 9), 'PossibleLengthLocalOnly' => array()), 'fixedLine' => array('NationalNumberPattern' => '3(?:0(?:1[0-2]|80)|282|3(?:8[1-9]|9[3-9])|611)\\d{5}', 'ExampleNumber' => '301012345', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'mobile' => array('NationalNumberPattern' => '7(?:[06-8]\\d|21|90)\\d{6}', 'ExampleNumber' => '701234567', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'tollFree' => array('NationalNumberPattern' => '800\\d{6}', 'ExampleNumber' => '800123456', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'premiumRate' => array('NationalNumberPattern' => '88[4689]\\d{6}', 'ExampleNumber' => '884123456', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'sharedCost' => array('NationalNumberPattern' => '81[02468]\\d{6}', 'ExampleNumber' => '810123456', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'personalNumber' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'voip' => array('NationalNumberPattern' => '93330\\d{4}|3(?:392|9[01]\\d)\\d{5}', 'ExampleNumber' => '933301234', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'pager' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'uan' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'voicemail' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'noInternationalDialling' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'id' => 'SN', 'countryCode' => 221, 'internationalPrefix' => '00', 'sameMobileAndFixedLinePattern' => \false, 'numberFormat' => array(0 => array('pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})', 'format' => '$1 $2 $3 $4', 'leadingDigitsPatterns' => array(0 => '8'), 'nationalPrefixFormattingRule' => '', 'domesticCarrierCodeFormattingRule' => '', 'nationalPrefixOptionalWhenFormatting' => \false), 1 => array('pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})', 'format' => '$1 $2 $3 $4', 'leadingDigitsPatterns' => array(0 => '[379]'), 'nationalPrefixFormattingRule' => '', 'domesticCarrierCodeFormattingRule' => '', 'nationalPrefixOptionalWhenFormatting' => \false)), 'intlNumberFormat' => array(), 'mainCountryForCode' => \false, 'leadingZeroPossible' => \false, 'mobileNumberPortableRegion' => \false);
