<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_MATCH().
 *
 * Returns the first substring(s) that match a POSIX regular expression pattern, or NULL if there is no match.
 *
 * @see https://www.postgresql.org/docs/17/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_MATCH(e.text, 'pattern', 'i') FROM Entity e"
 */
class RegexpMatch extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_match';
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
