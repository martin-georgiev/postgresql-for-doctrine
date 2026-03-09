<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL pg_trgm SIMILARITY().
 *
 * Returns a number indicating how similar the two arguments are.
 * The range is zero (completely dissimilar) to one (identical).
 * Requires the pg_trgm extension.
 *
 * @see https://www.postgresql.org/docs/current/pgtrgm.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SIMILARITY(e.name, :search) FROM Entity e"
 */
class Similarity extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('similarity(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
