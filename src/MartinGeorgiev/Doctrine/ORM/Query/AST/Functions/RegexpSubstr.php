<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_SUBSTR().
 *
 * Returns the substring within string that matches the Nth occurrence of the POSIX regular expression pattern,
 * or NULL if there is no such match.
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_SUBSTR(e.text, 'c(.)(..)', 1, 1, 'i', 2) FROM Entity e"
 */
class RegexpSubstr extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,SimpleArithmeticExpression,SimpleArithmeticExpression,StringPrimary,SimpleArithmeticExpression',
            'StringPrimary,StringPrimary,SimpleArithmeticExpression,SimpleArithmeticExpression,StringPrimary',
            'StringPrimary,StringPrimary,SimpleArithmeticExpression,SimpleArithmeticExpression',
            'StringPrimary,StringPrimary,SimpleArithmeticExpression',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_substr';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 6;
    }
}
