<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL pg_trgm %> operator.
 *
 * Commutator of the <% operator.
 * Returns true if the word similarity between the arguments is greater than the current word similarity threshold
 * (pg_trgm.word_similarity_threshold). The first argument is treated as the haystack, the second as the needle.
 *
 * @see https://www.postgresql.org/docs/18/pgtrgm.html#PGTRGM-FUNCS-OPS
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CONTAINS_WORD_SIMILAR_TO(e.name, :search) FROM Entity e"
 */
class ContainsWordSimilarTo extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s %%> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
