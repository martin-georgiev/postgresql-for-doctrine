<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_REPLACE().
 *
 * Replaces substring(s) matching a POSIX regular expression pattern with a replacement string.
 *
 * @see https://www.postgresql.org/docs/17/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.5
 *
 * @author Colin Doig
 *
 * @example Using it in DQL: "SELECT REGEXP_REPLACE(e.text, 'pattern', 'replacement', 3, 2, 'i') FROM Entity e"
 */
class RegexpReplace extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,StringPrimary,StringPrimary',
            'StringPrimary,StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_replace';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 6;
    }
}
