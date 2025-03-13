<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL TO_TSQUERY().
 *
 * @see https://www.postgresql.org/docs/9.4/static/textsearch-controls.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ToTsquery extends BaseVariadicFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_tsquery(%s)');
    }

    protected function validateArguments(array $arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1 || $argumentCount > 2) {
            throw InvalidArgumentForVariadicFunctionException::between('to_tsquery', 1, 2);
        }
    }
}
