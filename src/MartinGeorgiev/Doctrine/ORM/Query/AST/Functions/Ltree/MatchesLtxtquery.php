<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL ltree @ operator.
 *
 * Checks whether an ltree path matches an ltxtquery full-text label query.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE MATCHES_LTXTQUERY(e.path, 'Top & !Child2') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class MatchesLtxtquery extends BaseFunction
{
    protected function customizeFunction(): void
    {
        // The second argument is cast explicitly so that plain DQL string literals and parameters
        // resolve to ltxtquery instead of text, for which no @ operator against ltree exists.
        $this->setFunctionPrototype('(%s @ CAST(%s AS ltxtquery))');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
