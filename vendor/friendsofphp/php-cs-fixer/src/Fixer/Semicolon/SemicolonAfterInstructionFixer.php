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
namespace MolliePrefix\PhpCsFixer\Fixer\Semicolon;

use MolliePrefix\PhpCsFixer\AbstractFixer;
use MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample;
use MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition;
use MolliePrefix\PhpCsFixer\Tokenizer\Token;
use MolliePrefix\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class SemicolonAfterInstructionFixer extends \MolliePrefix\PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition('Instructions must be terminated with a semicolon.', [new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample("<?php echo 1 ?>\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_CLOSE_TAG);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = \count($tokens) - 1; $index > 1; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_CLOSE_TAG)) {
                continue;
            }
            $prev = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prev]->equalsAny([';', '{', '}', ':', [\T_OPEN_TAG]])) {
                continue;
            }
            $tokens->insertAt($prev + 1, new \MolliePrefix\PhpCsFixer\Tokenizer\Token(';'));
        }
    }
}
