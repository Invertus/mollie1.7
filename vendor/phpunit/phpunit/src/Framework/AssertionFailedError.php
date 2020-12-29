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
 * Thrown when an assertion failed.
 *
 * @since Class available since Release 2.0.0
 */
class PHPUnit_Framework_AssertionFailedError extends \MolliePrefix\PHPUnit_Framework_Exception implements \MolliePrefix\PHPUnit_Framework_SelfDescribing
{
    /**
     * Wrapper for getMessage() which is declared as final.
     *
     * @return string
     */
    public function toString()
    {
        return $this->getMessage();
    }
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
 * Thrown when an assertion failed.
 *
 * @since Class available since Release 2.0.0
 */
\class_alias('MolliePrefix\\PHPUnit_Framework_AssertionFailedError', 'PHPUnit_Framework_AssertionFailedError', \false);
