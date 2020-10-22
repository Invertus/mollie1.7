<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MolliePrefix\Prophecy\Call;

use MolliePrefix\Prophecy\Exception\Prophecy\MethodProphecyException;
use MolliePrefix\Prophecy\Prophecy\ObjectProphecy;
use MolliePrefix\Prophecy\Argument\ArgumentsWildcard;
use MolliePrefix\Prophecy\Util\StringUtil;
use MolliePrefix\Prophecy\Exception\Call\UnexpectedCallException;
use SplObjectStorage;
/**
 * Calls receiver & manager.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallCenter
{
    private $util;
    /**
     * @var Call[]
     */
    private $recordedCalls = array();
    /**
     * @var SplObjectStorage
     */
    private $unexpectedCalls;
    /**
     * Initializes call center.
     *
     * @param StringUtil $util
     */
    public function __construct(\MolliePrefix\Prophecy\Util\StringUtil $util = null)
    {
        $this->util = $util ?: new \MolliePrefix\Prophecy\Util\StringUtil();
        $this->unexpectedCalls = new \SplObjectStorage();
    }
    /**
     * Makes and records specific method call for object prophecy.
     *
     * @param ObjectProphecy $prophecy
     * @param string         $methodName
     * @param array          $arguments
     *
     * @return mixed Returns null if no promise for prophecy found or promise return value.
     *
     * @throws \Prophecy\Exception\Call\UnexpectedCallException If no appropriate method prophecy found
     */
    public function makeCall(\MolliePrefix\Prophecy\Prophecy\ObjectProphecy $prophecy, $methodName, array $arguments)
    {
        // For efficiency exclude 'args' from the generated backtrace
        if (\PHP_VERSION_ID >= 50400) {
            // Limit backtrace to last 3 calls as we don't use the rest
            // Limit argument was introduced in PHP 5.4.0
            $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        } elseif (\defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
            // DEBUG_BACKTRACE_IGNORE_ARGS was introduced in PHP 5.3.6
            $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $backtrace = \debug_backtrace();
        }
        $file = $line = null;
        if (isset($backtrace[2]) && isset($backtrace[2]['file'])) {
            $file = $backtrace[2]['file'];
            $line = $backtrace[2]['line'];
        }
        // If no method prophecies defined, then it's a dummy, so we'll just return null
        if ('__destruct' === \strtolower($methodName) || 0 == \count($prophecy->getMethodProphecies())) {
            $this->recordedCalls[] = new \MolliePrefix\Prophecy\Call\Call($methodName, $arguments, null, null, $file, $line);
            return null;
        }
        // There are method prophecies, so it's a fake/stub. Searching prophecy for this call
        $matches = $this->findMethodProphecies($prophecy, $methodName, $arguments);
        // If fake/stub doesn't have method prophecy for this call - throw exception
        if (!\count($matches)) {
            $this->unexpectedCalls->attach(new \MolliePrefix\Prophecy\Call\Call($methodName, $arguments, null, null, $file, $line), $prophecy);
            $this->recordedCalls[] = new \MolliePrefix\Prophecy\Call\Call($methodName, $arguments, null, null, $file, $line);
            return null;
        }
        // Sort matches by their score value
        @\usort($matches, function ($match1, $match2) {
            return $match2[0] - $match1[0];
        });
        $score = $matches[0][0];
        // If Highest rated method prophecy has a promise - execute it or return null instead
        $methodProphecy = $matches[0][1];
        $returnValue = null;
        $exception = null;
        if ($promise = $methodProphecy->getPromise()) {
            try {
                $returnValue = $promise->execute($arguments, $prophecy, $methodProphecy);
            } catch (\Exception $e) {
                $exception = $e;
            }
        }
        if ($methodProphecy->hasReturnVoid() && $returnValue !== null) {
            throw new \MolliePrefix\Prophecy\Exception\Prophecy\MethodProphecyException("The method \"{$methodName}\" has a void return type, but the promise returned a value", $methodProphecy);
        }
        $this->recordedCalls[] = $call = new \MolliePrefix\Prophecy\Call\Call($methodName, $arguments, $returnValue, $exception, $file, $line);
        $call->addScore($methodProphecy->getArgumentsWildcard(), $score);
        if (null !== $exception) {
            throw $exception;
        }
        return $returnValue;
    }
    /**
     * Searches for calls by method name & arguments wildcard.
     *
     * @param string            $methodName
     * @param ArgumentsWildcard $wildcard
     *
     * @return Call[]
     */
    public function findCalls($methodName, \MolliePrefix\Prophecy\Argument\ArgumentsWildcard $wildcard)
    {
        $methodName = \strtolower($methodName);
        return \array_values(\array_filter($this->recordedCalls, function (\MolliePrefix\Prophecy\Call\Call $call) use($methodName, $wildcard) {
            return $methodName === \strtolower($call->getMethodName()) && 0 < $call->getScore($wildcard);
        }));
    }
    /**
     * @throws UnexpectedCallException
     */
    public function checkUnexpectedCalls()
    {
        /** @var Call $call */
        foreach ($this->unexpectedCalls as $call) {
            $prophecy = $this->unexpectedCalls[$call];
            // If fake/stub doesn't have method prophecy for this call - throw exception
            if (!\count($this->findMethodProphecies($prophecy, $call->getMethodName(), $call->getArguments()))) {
                throw $this->createUnexpectedCallException($prophecy, $call->getMethodName(), $call->getArguments());
            }
        }
    }
    private function createUnexpectedCallException(\MolliePrefix\Prophecy\Prophecy\ObjectProphecy $prophecy, $methodName, array $arguments)
    {
        $classname = \get_class($prophecy->reveal());
        $indentationLength = 8;
        // looks good
        $argstring = \implode(",\n", $this->indentArguments(\array_map(array($this->util, 'stringify'), $arguments), $indentationLength));
        $expected = array();
        foreach (\call_user_func_array('array_merge', $prophecy->getMethodProphecies()) as $methodProphecy) {
            $expected[] = \sprintf("  - %s(\n" . "%s\n" . "    )", $methodProphecy->getMethodName(), \implode(",\n", $this->indentArguments(\array_map('strval', $methodProphecy->getArgumentsWildcard()->getTokens()), $indentationLength)));
        }
        return new \MolliePrefix\Prophecy\Exception\Call\UnexpectedCallException(\sprintf("Unexpected method call on %s:\n" . "  - %s(\n" . "%s\n" . "    )\n" . "expected calls were:\n" . "%s", $classname, $methodName, $argstring, \implode("\n", $expected)), $prophecy, $methodName, $arguments);
    }
    private function indentArguments(array $arguments, $indentationLength)
    {
        return \preg_replace_callback('/^/m', function () use($indentationLength) {
            return \str_repeat(' ', $indentationLength);
        }, $arguments);
    }
    /**
     * @param ObjectProphecy $prophecy
     * @param string $methodName
     * @param array $arguments
     *
     * @return array
     */
    private function findMethodProphecies(\MolliePrefix\Prophecy\Prophecy\ObjectProphecy $prophecy, $methodName, array $arguments)
    {
        $matches = array();
        foreach ($prophecy->getMethodProphecies($methodName) as $methodProphecy) {
            if (0 < ($score = $methodProphecy->getArgumentsWildcard()->scoreArguments($arguments))) {
                $matches[] = array($score, $methodProphecy);
            }
        }
        return $matches;
    }
}
