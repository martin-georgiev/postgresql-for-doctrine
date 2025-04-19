<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSONB_PATH_QUERY().
 *
 * Returns all JSON items returned by the JSON path for the specified JSON value.
 * Returns multiple rows, so it's useful when used in a subquery.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT e.id FROM Entity e WHERE e.id IN (SELECT JSONB_PATH_QUERY(e2.jsonbData, '$.items[*].id') FROM Entity2 e2)"
 */
class JsonbPathQuery extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_path_query';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        // Validate that the fourth parameter is a valid boolean if provided
        if (\count($arguments) === 4) {
            $this->validateBoolean($arguments[3], $this->getFunctionName());
        }
    }
}
