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

use MolliePrefix\phpDocumentor\Reflection\DocBlock\Tag\MethodTag as LegacyMethodTag;
use MolliePrefix\phpDocumentor\Reflection\DocBlock\Tags\Method;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
final class ClassAndInterfaceTagRetriever implements \MolliePrefix\Prophecy\PhpDocumentor\MethodTagRetrieverInterface
{
    private $classRetriever;
    public function __construct(\MolliePrefix\Prophecy\PhpDocumentor\MethodTagRetrieverInterface $classRetriever = null)
    {
        if (null !== $classRetriever) {
            $this->classRetriever = $classRetriever;
            return;
        }
        $this->classRetriever = \class_exists('MolliePrefix\\phpDocumentor\\Reflection\\DocBlockFactory') && \class_exists('MolliePrefix\\phpDocumentor\\Reflection\\Types\\ContextFactory') ? new \MolliePrefix\Prophecy\PhpDocumentor\ClassTagRetriever() : new \MolliePrefix\Prophecy\PhpDocumentor\LegacyClassTagRetriever();
    }
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyMethodTag[]|Method[]
     */
    public function getTagList(\ReflectionClass $reflectionClass)
    {
        return \array_merge($this->classRetriever->getTagList($reflectionClass), $this->getInterfacesTagList($reflectionClass));
    }
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyMethodTag[]|Method[]
     */
    private function getInterfacesTagList(\ReflectionClass $reflectionClass)
    {
        $interfaces = $reflectionClass->getInterfaces();
        $tagList = array();
        foreach ($interfaces as $interface) {
            $tagList = \array_merge($tagList, $this->classRetriever->getTagList($interface));
        }
        return $tagList;
    }
}
