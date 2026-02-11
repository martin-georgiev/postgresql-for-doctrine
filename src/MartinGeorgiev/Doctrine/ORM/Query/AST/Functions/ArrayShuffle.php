<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_SHUFFLE().
 *
 * Randomly shuffles the elements of an array.
 *
 * @see https://www.postgresql.org/docs/17/functions-array.html
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_SHUFFLE(e.array) FROM Entity e"
 */
class ArrayShuffle extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_shuffle(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
