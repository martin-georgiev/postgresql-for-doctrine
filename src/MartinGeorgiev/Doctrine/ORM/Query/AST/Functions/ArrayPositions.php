<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_POSITIONS().
 *
 * @see https://www.postgresql.org/docs/9.5/static/functions-array.html
 * @since 3.1
 *
 * @author Daniel Gorgan <danut007ro@gmail.com>
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
