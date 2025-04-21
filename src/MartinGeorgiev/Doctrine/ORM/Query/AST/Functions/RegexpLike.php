<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_LIKE().
 *
 * Returns true if a string matches a POSIX regular expression pattern, or false if it does not.
 *
 * @see https://www.postgresql.org/docs/17/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_LIKE(e.text, 'pattern', 3, 'i') FROM Entity e"
 */
class RegexpLike extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,StringPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,StringPrimary',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_like';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
