<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_TO_JSON().
 *
 * Returns the array as a JSON array. A PostgreSQL multidimensional array becomes a JSON array of arrays.
 * Line feeds will be added between dimension-1 elements if pretty_bool is true.
 *
 * @see https://www.postgresql.org/docs/16/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_TO_JSON(e.textArray) FROM Entity e"
 * @example Using it in DQL: "SELECT ARRAY_TO_JSON(e.textArray, 'true') FROM Entity e"
 */
class ArrayToJson extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'array_to_json';
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
