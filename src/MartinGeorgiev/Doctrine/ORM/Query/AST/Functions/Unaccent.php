<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL UNACCENT.
 *
 * @see https://www.postgresql.org/docs/17/unaccent.html
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class Unaccent extends BaseVariadicFunction
{
    protected string $commonNodeMapping = 'StringPrimary';

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('unaccent(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1 || $argumentCount > 2) {
            throw InvalidArgumentForVariadicFunctionException::between('unaccent', 1, 2);
        }
    }
}
