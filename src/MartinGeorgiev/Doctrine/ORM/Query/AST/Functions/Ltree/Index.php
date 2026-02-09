<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostgreSQL index function for ltree.
 *
 * Returns position of first occurrence of b in a, or -1 if not found.
 * The search starts at position offset; negative offset means start -offset labels from the end of the path.
 *
 * @see https://www.postgresql.org/docs/17/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT INDEX(e.path, 'Child1') FROM Entity e"
 */
class Index extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,SimpleArithmeticExpression',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'index';
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
