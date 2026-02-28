<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_TO_TSVECTOR().
 *
 * Converts a JSON value to a text search vector.
 *
 * @see https://www.postgresql.org/docs/18/functions-textsearch.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_TO_TSVECTOR(e.jsonData, '[\"string\"]') FROM Entity e"
 * @example Using it in DQL with config: "SELECT JSON_TO_TSVECTOR('english', e.jsonData, '[\"string\", \"numeric\"]') FROM Entity e"
 */
class JsonToTsvector extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'json_to_tsvector';
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
