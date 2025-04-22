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
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_REPLACE(e.text, 'pattern', 'replacement', 3, 2, 'i') FROM Entity e"
 */
class RegexpReplace extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        /*
         * PostgreSQL overloads the 4th argument depending on its type:
         *   - if the 4th arg is a string, it’s taken as flags.
         *   - if the 4th arg is an integer, it’s taken as start position. This can be extended with the Nth argument.
         */
        return [
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary,StringPrimary', // with start, N and flags: regexp_replace(string, pattern, replacement, 3, 2, 'i')
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary', // with start and N: regexp_replace(string, pattern, replacement, 3, 2)
            'StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary', // with start: regexp_replace(string, pattern, replacement, 3)
            'StringPrimary,StringPrimary,StringPrimary,StringPrimary', // with flags: regexp_replace(string, pattern, replacement, 'i')
            'StringPrimary,StringPrimary,StringPrimary', // basic replacement: regexp_replace(string, pattern, replacement)
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
