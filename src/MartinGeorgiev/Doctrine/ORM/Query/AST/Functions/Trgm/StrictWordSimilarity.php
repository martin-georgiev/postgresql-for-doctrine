<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL pg_trgm STRICT_WORD_SIMILARITY().
 *
 * Returns a number indicating the greatest similarity between the first string and any continuous extent
 * of words of the second string, forcing extent boundaries to match word boundaries.
 * Requires the pg_trgm extension.
 *
 * @see https://www.postgresql.org/docs/current/pgtrgm.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT STRICT_WORD_SIMILARITY(e.name, :search) FROM Entity e"
 */
class StrictWordSimilarity extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('strict_word_similarity(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
