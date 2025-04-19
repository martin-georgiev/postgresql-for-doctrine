<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSONB_PATH_QUERY_FIRST().
 *
 * Returns the first JSON item returned by the JSON path for the specified JSON value.
 * Returns NULL if there are no results.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_PATH_QUERY_FIRST(e.jsonbData, '$.items[*] ? (@.price > 100)') FROM Entity e"
 */
class JsonbPathQueryFirst extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'jsonb_path_query_first';
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
