<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSONB_STRIP_NULLS().
 *
 * Removes all object fields with null values from the given JSONB value.
 * Optionally controls whether to strip nulls from arrays (PostgreSQL 18+).
 *
 * @see https://www.postgresql.org/docs/18/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with basic usage: "SELECT JSONB_STRIP_NULLS(e.data) FROM Entity e"
 * @example Using it in DQL with null stripping from arrays (PostgreSQL 18+): "SELECT JSONB_STRIP_NULLS(e.data, 'true') FROM Entity e"
 */
class JsonbStripNulls extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getFunctionName(): string
    {
        return 'jsonb_strip_nulls';
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

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        if (\count($arguments) === 2) {
            $this->validateBoolean($arguments[1], $this->getFunctionName());
        }
    }
}
