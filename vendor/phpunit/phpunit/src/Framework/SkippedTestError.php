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
 * Extension to PHPUnit_Framework_AssertionFailedError to mark the special
 * case of a skipped test.
 */
class PHPUnit_Framework_SkippedTestError extends \MolliePrefix\PHPUnit_Framework_AssertionFailedError implements \MolliePrefix\PHPUnit_Framework_SkippedTest
{
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
 * Extension to PHPUnit_Framework_AssertionFailedError to mark the special
 * case of a skipped test.
 */
\class_alias('MolliePrefix\\PHPUnit_Framework_SkippedTestError', 'MolliePrefix\\PHPUnit_Framework_SkippedTestError', \false);
