<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ROW_TO_JSON().
 *
 * Returns the row as a JSON object. Line feeds will be added between level-1 elements if pretty_bool is true.
 *
 * @see https://www.postgresql.org/docs/16/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ROW_TO_JSON(e.row) FROM Entity e"
 * @example Using it in DQL with pretty_bool: "SELECT ROW_TO_JSON(e.row, 'true') FROM Entity e"
 */
class RowToJson extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'row_to_json';
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
