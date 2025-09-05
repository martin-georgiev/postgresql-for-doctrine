<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostgreSQL subpath function.
 *
 * Returns subpath of ltree starting at position offset, with length len.
 * If offset is negative, subpath starts that far from the end of the path.
 * If len is negative, leaves that many labels off the end of the path.
 *
 * @see https://www.postgresql.org/docs/current/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SUBPATH(e.path, 0, 2) FROM Entity e"
 * Returns ltree, subpath starting at position offset, with length len.
 */
class Subpath extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,SimpleArithmeticExpression,SimpleArithmeticExpression'];
    }

    protected function getFunctionName(): string
    {
        return 'subpath';
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
