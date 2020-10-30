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
return array('generalDesc' => array('NationalNumberPattern' => '[1-6]\\d{7}', 'PossibleLength' => array(0 => 8), 'PossibleLengthLocalOnly' => array()), 'fixedLine' => array('NationalNumberPattern' => '(?:1(?:2\\d|3[1-9])|2(?:22|4[0-35-8])|3(?:22|4[03-9])|4(?:22|3[128]|4\\d|6[15])|5(?:22|5[7-9]|6[014-689]))\\d{5}', 'ExampleNumber' => '12345678', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'mobile' => array('NationalNumberPattern' => '6\\d{7}', 'ExampleNumber' => '66123456', 'PossibleLength' => array(), 'PossibleLengthLocalOnly' => array()), 'tollFree' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'premiumRate' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'sharedCost' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'personalNumber' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'voip' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'pager' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'uan' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'voicemail' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'noInternationalDialling' => array('PossibleLength' => array(0 => -1), 'PossibleLengthLocalOnly' => array()), 'id' => 'TM', 'countryCode' => 993, 'internationalPrefix' => '810', 'preferredInternationalPrefix' => '8~10', 'nationalPrefix' => '8', 'nationalPrefixForParsing' => '8', 'sameMobileAndFixedLinePattern' => \false, 'numberFormat' => array(0 => array('pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})', 'format' => '$1 $2-$3-$4', 'leadingDigitsPatterns' => array(0 => '12'), 'nationalPrefixFormattingRule' => '(8 $1)', 'domesticCarrierCodeFormattingRule' => '', 'nationalPrefixOptionalWhenFormatting' => \false), 1 => array('pattern' => '(\\d{3})(\\d)(\\d{2})(\\d{2})', 'format' => '$1 $2-$3-$4', 'leadingDigitsPatterns' => array(0 => '[1-5]'), 'nationalPrefixFormattingRule' => '(8 $1)', 'domesticCarrierCodeFormattingRule' => '', 'nationalPrefixOptionalWhenFormatting' => \false), 2 => array('pattern' => '(\\d{2})(\\d{6})', 'format' => '$1 $2', 'leadingDigitsPatterns' => array(0 => '6'), 'nationalPrefixFormattingRule' => '8 $1', 'domesticCarrierCodeFormattingRule' => '', 'nationalPrefixOptionalWhenFormatting' => \false)), 'intlNumberFormat' => array(), 'mainCountryForCode' => \false, 'leadingZeroPossible' => \false, 'mobileNumberPortableRegion' => \false);
