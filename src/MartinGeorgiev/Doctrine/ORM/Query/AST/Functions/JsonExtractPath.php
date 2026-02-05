<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_EXTRACT_PATH().
 *
 * Extracts a value at a specified path in a JSON object.
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_EXTRACT_PATH(e.jsonData, 'key1', 'key2') FROM Entity e"
 */
class JsonExtractPath extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'json_extract_path';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX;
    }
}
