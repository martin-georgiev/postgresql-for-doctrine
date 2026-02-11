<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Implementation of PostgreSQL ARRAY_FILL().
 *
 * Creates an array filled with copies of the given value, with dimensions specified by the second argument.
 * An optional third argument specifies custom lower-bound values for each dimension (defaults to 1).
 *
 * @see https://www.postgresql.org/docs/18/functions-array.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Basic usage: "SELECT ARRAY_FILL(42, ARRAY('5')) FROM Entity e" returns {42,42,42,42,42}
 * @example With string value: "SELECT ARRAY_FILL(CAST('x' AS TEXT), ARRAY('3')) FROM Entity e" returns {x,x,x}
 * @example Multi-dimensional: "SELECT ARRAY_FILL(11, ARRAY('2', '3')) FROM Entity e" returns {{11,11,11},{11,11,11}}
 * @example With lower bounds: "SELECT ARRAY_FILL(7, ARRAY('3'), ARRAY('2')) FROM Entity e" returns [2:4]={7,7,7}
 */
class ArrayFill extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'NewValue,StringPrimary,StringPrimary',
            'NewValue,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'array_fill';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $value = $this->nodes[0] instanceof Node ? $this->nodes[0]->dispatch($sqlWalker) : 'null';
        $dimensions = $this->nodes[1] instanceof Node ? $this->nodes[1]->dispatch($sqlWalker) : 'null';

        if (\count($this->nodes) === 3) {
            $lowerBounds = $this->nodes[2] instanceof Node ? $this->nodes[2]->dispatch($sqlWalker) : 'null';

            return \sprintf('array_fill(%s, %s::int[], %s::int[])', $value, $dimensions, $lowerBounds);
        }

        return \sprintf('array_fill(%s, %s::int[])', $value, $dimensions);
    }
}
