<?php

namespace MolliePrefix;

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Wrapper for PHP notices.
 * You can disable notice-to-exception conversion by setting
 *
 * <code>
 * PHPUnit_Framework_Error_Notice::$enabled = false;
 * </code>
 */
class PHPUnit_Framework_Error_Notice extends \MolliePrefix\PHPUnit_Framework_Error
{
    public static $enabled = \true;
}
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Wrapper for PHP notices.
 * You can disable notice-to-exception conversion by setting
 *
 * <code>
 * PHPUnit_Framework_Error_Notice::$enabled = false;
 * </code>
 */
\class_alias('MolliePrefix\\PHPUnit_Framework_Error_Notice', 'PHPUnit_Framework_Error_Notice', \false);
