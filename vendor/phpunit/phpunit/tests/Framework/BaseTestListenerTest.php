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
class Framework_BaseTestListenerTest extends \MolliePrefix\PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_TestResult
     */
    private $result;
    public function testEndEventsAreCounted()
    {
        $this->result = new \MolliePrefix\PHPUnit_Framework_TestResult();
        $listener = new \MolliePrefix\BaseTestListenerSample();
        $this->result->addListener($listener);
        $test = new \MolliePrefix\Success();
        $test->run($this->result);
        $this->assertEquals(1, $listener->endCount);
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
\class_alias('MolliePrefix\\Framework_BaseTestListenerTest', 'Framework_BaseTestListenerTest', \false);
