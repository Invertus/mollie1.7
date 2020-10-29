<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MolliePrefix\Prophecy\PhpDocumentor;

use MolliePrefix\phpDocumentor\Reflection\DocBlock;
use MolliePrefix\phpDocumentor\Reflection\DocBlock\Tag\MethodTag as LegacyMethodTag;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
final class LegacyClassTagRetriever implements \MolliePrefix\Prophecy\PhpDocumentor\MethodTagRetrieverInterface
{
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyMethodTag[]
     */
    public function getTagList(\ReflectionClass $reflectionClass)
    {
        $phpdoc = new \MolliePrefix\phpDocumentor\Reflection\DocBlock($reflectionClass->getDocComment());
        return $phpdoc->getTagsByName('method');
    }
}
