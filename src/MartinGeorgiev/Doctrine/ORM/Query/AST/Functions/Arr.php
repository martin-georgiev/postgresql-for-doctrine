<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL ARRAY[].
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Arr extends BaseVariadicFunction
{
    protected string $commonNodeMapping = 'StringPrimary';

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ARRAY[%s]');
    }

    protected function validateArguments(array $arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount === 0) {
            throw InvalidArgumentForVariadicFunctionException::atLeast('ARRAY', 1);
        }
    }
}
