<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ANY_VALUE().
 *
 * Returns an arbitrary value from the input set.
 *
 * @see https://www.postgresql.org/docs/16/functions-aggregate.html
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ANY_VALUE(e.name) FROM Entity e"
 */
class AnyValue extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('any_value(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
