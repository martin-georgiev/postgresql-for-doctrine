<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL JSON_BUILD_OBJECT().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonBuildObject extends BaseVariadicFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_build_object(%s)');
    }

    protected function validateArguments(array $arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount === 0 || $argumentCount % 2 !== 0) {
            throw InvalidArgumentForVariadicFunctionException::evenNumber('json_build_object');
        }
    }
}
