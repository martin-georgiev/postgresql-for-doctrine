<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL JSON_SERIALIZE().
 *
 * Supports basic form:
 * - json_serialize(expression)
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonSerialize extends BaseVariadicFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_serialize(%s)');
    }

    protected function validateArguments(array $arguments): void
    {
        if (\count($arguments) !== 1) {
            throw InvalidArgumentForVariadicFunctionException::exact('json_serialize', 1);
        }
    }
}
