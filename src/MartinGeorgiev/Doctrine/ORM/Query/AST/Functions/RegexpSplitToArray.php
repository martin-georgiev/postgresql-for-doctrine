<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_SPLIT_TO_ARRAY().
 *
 * Splits a string into an array, using a POSIX regular expression as the delimiter.
 *
 * @see https://www.postgresql.org/docs/18/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_SPLIT_TO_ARRAY(e.text, '\s*,\s*') FROM Entity e"
 * @example Using it in DQL with flags: "SELECT REGEXP_SPLIT_TO_ARRAY(e.text, 'and', 'i') FROM Entity e"
 */
class RegexpSplitToArray extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_split_to_array';
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
