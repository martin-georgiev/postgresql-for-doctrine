<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL pg_trgm %>> operator.
 *
 * Commutator of the <<% operator.
 * Returns true if the strict word similarity between the arguments is greater
 * than the current strict word similarity threshold (pg_trgm.strict_word_similarity_threshold).
 * Forces extent boundaries to match word boundaries.
 * The first argument is the haystack, the second as the needle.
 *
 * @see https://www.postgresql.org/docs/18/pgtrgm.html#PGTRGM-FUNCS-OPS
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE CONTAINS_STRICT_WORD_SIMILAR_TO(e.name, :search) = TRUE"
 */
class ContainsStrictWordSimilarTo extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s %%>> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
