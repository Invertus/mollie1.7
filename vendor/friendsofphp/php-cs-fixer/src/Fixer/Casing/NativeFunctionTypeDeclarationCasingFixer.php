<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace MolliePrefix\PhpCsFixer\Fixer\Casing;

use MolliePrefix\PhpCsFixer\AbstractFixer;
use MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample;
use MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition;
use MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification;
use MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use MolliePrefix\PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use MolliePrefix\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use MolliePrefix\PhpCsFixer\Tokenizer\Token;
use MolliePrefix\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class NativeFunctionTypeDeclarationCasingFixer extends \MolliePrefix\PhpCsFixer\AbstractFixer
{
    /**
     * https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.
     *
     * self     PHP 5.0.0
     * array    PHP 5.1.0
     * callable PHP 5.4.0
     * bool     PHP 7.0.0
     * float    PHP 7.0.0
     * int      PHP 7.0.0
     * string   PHP 7.0.0
     * iterable PHP 7.1.0
     * void     PHP 7.1.0
     * object   PHP 7.2.0
     * static   PHP 8.0.0 (return type only)
     *
     * @var array<string, true>
     */
    private $hints;
    /**
     * @var FunctionsAnalyzer
     */
    private $functionsAnalyzer;
    public function __construct()
    {
        parent::__construct();
        $this->hints = ['array' => \true, 'callable' => \true, 'self' => \true];
        if (\PHP_VERSION_ID >= 70000) {
            $this->hints = \array_merge($this->hints, ['bool' => \true, 'float' => \true, 'int' => \true, 'string' => \true]);
        }
        if (\PHP_VERSION_ID >= 70100) {
            $this->hints = \array_merge($this->hints, ['iterable' => \true, 'void' => \true]);
        }
        if (\PHP_VERSION_ID >= 70200) {
            $this->hints = \array_merge($this->hints, ['object' => \true]);
        }
        if (\PHP_VERSION_ID >= 80000) {
            $this->hints = \array_merge($this->hints, ['static' => \true]);
        }
        $this->functionsAnalyzer = new \MolliePrefix\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition('Native type hints for functions should use the correct case.', [new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample("<?php\nclass Bar {\n    public function Foo(CALLABLE \$bar)\n    {\n        return 1;\n    }\n}\n"), new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction Foo(INT \$a): Bool\n{\n    return true;\n}\n", new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification(70000)), new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction Foo(Iterable \$a): VOID\n{\n    echo 'Hello world';\n}\n", new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification(70100)), new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction Foo(Object \$a)\n{\n    return 'hi!';\n}\n", new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification(70200))]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_FUNCTION, \T_STRING]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind(\T_FUNCTION)) {
                if (\PHP_VERSION_ID >= 70000) {
                    $this->fixFunctionReturnType($tokens, $index);
                }
                $this->fixFunctionArgumentTypes($tokens, $index);
            }
        }
    }
    /**
     * @param int $index
     */
    private function fixFunctionArgumentTypes(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        foreach ($this->functionsAnalyzer->getFunctionArguments($tokens, $index) as $argument) {
            $this->fixArgumentType($tokens, $argument->getTypeAnalysis());
        }
    }
    /**
     * @param int $index
     */
    private function fixFunctionReturnType(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $this->fixArgumentType($tokens, $this->functionsAnalyzer->getFunctionReturnType($tokens, $index));
    }
    private function fixArgumentType(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, \MolliePrefix\PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis $type = null)
    {
        if (null === $type) {
            return;
        }
        $argumentStartIndex = $type->getStartIndex();
        $argumentExpectedEndIndex = $type->isNullable() ? $tokens->getNextMeaningfulToken($argumentStartIndex) : $argumentStartIndex;
        if ($argumentExpectedEndIndex !== $type->getEndIndex()) {
            return;
            // the type to fix is always unqualified and so is always composed of one token and possible a nullable '?' one
        }
        $lowerCasedName = \strtolower($type->getName());
        if (!isset($this->hints[$lowerCasedName])) {
            return;
            // check of type is of interest based on name (slower check than previous index based)
        }
        $tokens[$argumentExpectedEndIndex] = new \MolliePrefix\PhpCsFixer\Tokenizer\Token([$tokens[$argumentExpectedEndIndex]->getId(), $lowerCasedName]);
    }
}
