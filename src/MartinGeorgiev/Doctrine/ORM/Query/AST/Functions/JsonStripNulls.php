<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_STRIP_NULLS().
 *
 * Supports optional second parameter (PostgreSQL 18+) to control null stripping from arrays.
 *
 * @see https://www.postgresql.org/docs/18/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonStripNulls extends BaseVariadicFunction
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
