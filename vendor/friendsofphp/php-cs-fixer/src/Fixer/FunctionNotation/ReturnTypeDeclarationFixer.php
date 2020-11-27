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
namespace MolliePrefix\PhpCsFixer\Fixer\FunctionNotation;

use MolliePrefix\PhpCsFixer\AbstractFixer;
use MolliePrefix\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use MolliePrefix\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition;
use MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification;
use MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use MolliePrefix\PhpCsFixer\Tokenizer\CT;
use MolliePrefix\PhpCsFixer\Tokenizer\Token;
use MolliePrefix\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ReturnTypeDeclarationFixer extends \MolliePrefix\PhpCsFixer\AbstractFixer implements \MolliePrefix\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $versionSpecification = new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecification(70000);
        return new \MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition('There should be one or no space before colon, and one space after it in return type declarations, according to configuration.', [new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int \$a):string {};\n", $versionSpecification), new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int \$a):string {};\n", $versionSpecification, ['space_before' => 'none']), new \MolliePrefix\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int \$a):string {};\n", $versionSpecification, ['space_before' => 'one'])], 'Rule is applied only in a PHP 7+ environment.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run after PhpdocToReturnTypeFixer, VoidReturnFixer.
     */
    public function getPriority()
    {
        return -17;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(\MolliePrefix\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $oneSpaceBefore = 'one' === $this->configuration['space_before'];
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            if (!$tokens[$index]->isGivenKind(\MolliePrefix\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON)) {
                continue;
            }
            $previousIndex = $index - 1;
            $previousToken = $tokens[$previousIndex];
            if ($previousToken->isWhitespace()) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                    if ($oneSpaceBefore) {
                        $tokens[$previousIndex] = new \MolliePrefix\PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
                    } else {
                        $tokens->clearAt($previousIndex);
                    }
                }
            } elseif ($oneSpaceBefore) {
                $tokenWasAdded = $tokens->ensureWhitespaceAtIndex($index, 0, ' ');
                if ($tokenWasAdded) {
                    ++$limit;
                }
                ++$index;
            }
            ++$index;
            $tokenWasAdded = $tokens->ensureWhitespaceAtIndex($index, 0, ' ');
            if ($tokenWasAdded) {
                ++$limit;
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('space_before', 'Spacing to apply before colon.'))->setAllowedValues(['one', 'none'])->setDefault('none')->getOption()]);
    }
}
