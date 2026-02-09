<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL UNNEST().
 *
 * Expands an array into individual rows.
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT UNNEST(e.array) FROM Entity e"
 */
class Unnest extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('unnest(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
