<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_STRIP_NULLS().
 *
 *  Removes all object fields with null values from the given JSON value.
 *  Optionally controls whether to strip nulls from arrays (PostgreSQL 18+).
 *
 * @see https://www.postgresql.org/docs/18/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with basic usage: "SELECT JSON_STRIP_NULLS(e.data) FROM Entity e"
 * @example Using it in DQL with null stripping from arrays (PostgreSQL 18+): "SELECT JSON_STRIP_NULLS(e.data, 'true') FROM Entity e"
 */
class JsonStripNulls extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getFunctionName(): string
    {
        return 'json_strip_nulls';
    }

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary',
        ];
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
