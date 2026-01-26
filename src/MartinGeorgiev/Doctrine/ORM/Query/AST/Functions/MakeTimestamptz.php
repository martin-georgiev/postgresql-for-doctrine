<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL MAKE_TIMESTAMPTZ().
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0, 'UTC') FROM Entity e"
 */
class MakeTimestamptz extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'SimpleArithmeticExpression,SimpleArithmeticExpression,SimpleArithmeticExpression,SimpleArithmeticExpression,SimpleArithmeticExpression,SimpleArithmeticExpression,StringPrimary',
            'SimpleArithmeticExpression',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'make_timestamptz';
    }

    protected function getMinArgumentCount(): int
    {
        return 6;
    }

    protected function getMaxArgumentCount(): int
    {
        return 7;
    }
}

