<?php

namespace MolliePrefix;

class CoverageFunctionTest extends \MolliePrefix\PHPUnit_Framework_TestCase
{
    /**
     * @covers ::globalFunction
     */
    public function testSomething()
    {
        \MolliePrefix\globalFunction();
    }
}
\class_alias('MolliePrefix\\CoverageFunctionTest', 'CoverageFunctionTest', \false);
