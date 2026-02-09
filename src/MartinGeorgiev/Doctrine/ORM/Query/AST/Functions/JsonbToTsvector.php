<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_TO_TSVECTOR().
 *
 * Converts a JSONB value to a text search vector.
 *
 * @see https://www.postgresql.org/docs/17/functions-textsearch.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_TO_TSVECTOR('english', e.jsonbData) FROM Entity e"
 * @example Using it in DQL with filter: "SELECT JSONB_TO_TSVECTOR('english', e.jsonbData, '["string", "numeric"]') FROM Entity e"
 */
class JsonbToTsvector extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_to_tsvector';
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
