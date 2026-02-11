<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * DQL wrapper for PostgreSQL ANY operator.
 *
 * Checks if a value equals any element in an array using the ANY operator.
 *
 * @see https://www.postgresql.org/docs/18/functions-comparisons.html#FUNCTIONS-COMPARISONS-ANY-SOME
 * @since 0.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT e.id FROM Entity e WHERE IN_ARRAY(e.confirmedValue, e.aliasValues) = TRUE"
 */
class InArray extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s = ANY(%s)');
        $this->addNodeMapping('InputParameter');
        $this->addNodeMapping('StringPrimary');
    }
}
