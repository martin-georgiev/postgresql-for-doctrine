<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_PATH_MATCH().
 *
 * Returns the SQL boolean result of a JSON path predicate check for the specified JSON value.
 * This is useful only with predicate check expressions, not SQL-standard JSON path expressions,
 * since it will either fail or return NULL if the path result is not a single boolean value.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_PATH_MATCH(e.jsonbData, 'exists($.a[*] ? (@ >= 2 && @ <= 4))')"
 */
class JsonbPathMatch extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_path_match';
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
