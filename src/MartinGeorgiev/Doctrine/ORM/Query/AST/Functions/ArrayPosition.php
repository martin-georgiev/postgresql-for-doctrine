<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_POSITION().
 *
 * Returns the position of the first occurrence of a value in an array.
 *
 * @see https://www.postgresql.org/docs/9.5/static/functions-array.html
 * @since 3.1
 *
 * @author Daniel Gorgan <danut007ro@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_POSITION(e.array, 5) FROM Entity e"
 */
class ArrayPosition extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,NewValue,SimpleArithmeticExpression',
            'StringPrimary,NewValue',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'array_position';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
