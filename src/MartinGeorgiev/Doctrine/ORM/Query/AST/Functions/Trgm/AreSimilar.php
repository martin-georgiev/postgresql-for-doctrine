<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL pg_trgm % operator.
 *
 * Returns true if the similarity between the arguments is greater than
 * the current similarity threshold (pg_trgm.similarity_threshold).
 *
 * @see https://www.postgresql.org/docs/18/pgtrgm.html#PGTRGM-FUNCS-OPS
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE ARE_SIMILAR(e.name, :search) = TRUE"
 */
class AreSimilar extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s %% %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
