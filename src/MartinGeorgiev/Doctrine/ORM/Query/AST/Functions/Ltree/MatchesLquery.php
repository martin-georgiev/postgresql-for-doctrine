<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL ltree ~ operator.
 *
 * Checks whether an ltree path matches an lquery path pattern.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE MATCHES_LQUERY(e.path, 'Top.*{0,2}.sport*@') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class MatchesLquery extends BaseFunction
{
    protected function customizeFunction(): void
    {
        // The second argument is cast explicitly so that plain DQL string literals and parameters
        // resolve to lquery instead of text, which has its own, unrelated ~ regular-expression operator.
        $this->setFunctionPrototype('(%s ~ CAST(%s AS lquery))');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
