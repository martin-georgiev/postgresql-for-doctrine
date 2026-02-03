<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_EXTRACT_PATH_TEXT().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_EXTRACT_PATH_TEXT(e.jsonbData, 'key1', 'key2') FROM Entity e"
 */
class JsonbExtractPathText extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_extract_path_text';
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
