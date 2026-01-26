<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL ARRAY_TO_JSONB().
 *
 * Returns the array as a JSONB array. A PostgreSQL multidimensional array becomes a JSONB array of arrays.
 * Line feeds will be added between dimension-1 elements if pretty_bool is true.
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_TO_JSONB(e.textArray) FROM Entity e"
 * @example Using it in DQL with pretty_bool: "SELECT ARRAY_TO_JSONB(e.textArray, 'true') FROM Entity e"
 */
class ArrayToJsonb extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'array_to_jsonb';
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

        // Validate that the second parameter is a valid boolean if provided
        if (\count($arguments) === 2) {
            $this->validateBoolean($arguments[1], $this->getFunctionName());
        }
    }
}
