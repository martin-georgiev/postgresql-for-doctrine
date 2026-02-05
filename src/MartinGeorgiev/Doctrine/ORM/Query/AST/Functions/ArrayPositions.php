<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_POSITIONS().
 *
 * Returns all positions of a value in an array.
 *
 * @see https://www.postgresql.org/docs/9.5/static/functions-array.html
 * @since 3.1
 *
 * @author Daniel Gorgan <danut007ro@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_POSITIONS(e.array, 5) FROM Entity e"
 */
class ArrayPositions extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_positions(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('NewValue');
    }
}
