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
namespace MolliePrefix\PhpCsFixer\Fixer\Comment;

use MolliePrefix\PhpCsFixer\AbstractProxyFixer;
use MolliePrefix\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample;
use MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * Changes single comments prefixes '#' with '//'.
 *
 * @author SpacePossum
 *
 * @deprecated in 2.4, proxy to SingleLineCommentStyleFixer
 */
final class HashToSlashCommentFixer extends \MolliePrefix\PhpCsFixer\AbstractProxyFixer implements \MolliePrefix\PhpCsFixer\Fixer\DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new \MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition('Single line comments should use double slashes `//` and not hash `#`.', [new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample("<?php # comment\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames()
    {
        return \array_keys($this->proxyFixers);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        $fixer = new \MolliePrefix\PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer();
        $fixer->configure(['comment_types' => ['hash']]);
        return [$fixer];
    }
}
