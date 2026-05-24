<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RPAD().
 *
 * Pads a string on the right to a specified length, optionally using a fill string.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RPAD(e.text1, 10, '0') FROM Entity e"
 */
class Rpad extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,SimpleArithmeticExpression,StringPrimary',
            'StringPrimary,SimpleArithmeticExpression',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'rpad';
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
