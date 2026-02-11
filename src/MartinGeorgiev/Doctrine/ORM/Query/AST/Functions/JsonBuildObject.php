<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL JSON_BUILD_OBJECT().
 *
 * Constructs a JSON object from key-value pairs.
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSON_BUILD_OBJECT('key', e.value) FROM Entity e"
 */
class JsonBuildObject extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'json_build_object';
    }

    protected function getMinArgumentCount(): int
    {
        return 2; // At least one key-value pair
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX; // No upper limit, but must be even
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        if (\count($arguments) % 2 !== 0) {
            throw InvalidArgumentForVariadicFunctionException::evenNumber($this->getFunctionName());
        }
    }
}
