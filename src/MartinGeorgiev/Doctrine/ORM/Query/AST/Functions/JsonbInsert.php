<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_INSERT().
 *
 * Inserts a new value into a JSONB field at the specified path.
 * If the path already exists, the value is not changed unless the last parameter is true.
 *
 * @see https://www.postgresql.org/docs/16/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with path and value: "SELECT JSONB_INSERT(e.jsonbData, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM Entity e"
 * @example Using it in DQL with insert_after flag: "SELECT JSONB_INSERT(e.jsonbData, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}', 'true') FROM Entity e"
 */
class JsonbInsert extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_insert';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
