<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL pg_trgm <-> operator.
 *
 * Returns the "distance" between the arguments, that is one minus the similarity() value.
 *
 * @see https://www.postgresql.org/docs/18/pgtrgm.html#PGTRGM-FUNCS-OPS
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SIMILARITY_DISTANCE(e.name, :search) FROM Entity e ORDER BY SIMILARITY_DISTANCE(e.name, :search)"
 */
class SimilarityDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s <-> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
