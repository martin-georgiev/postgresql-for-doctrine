<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_TIMESTAMP().
 *
 * Converts a string to a timestamp using a format, or a Unix epoch to a timestamp with time zone.
 *
 * @see https://www.postgresql.org/docs/17/functions-formatting.html
 * @since 3.3
 *
 * @author Andrei Karpilin <karpilin@gmail.com>
 *
 * @example Using it in DQL: "SELECT TO_TIMESTAMP(e.text, 'YYYY-MM-DD HH24:MI:SS') FROM Entity e"
 * @example Using it in DQL with a Unix epoch: "SELECT TO_TIMESTAMP(e.seconds) FROM Entity e"
 */
class ToTimestamp extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary',
            'SimpleArithmeticExpression',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'to_timestamp';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
