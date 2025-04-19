<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ARRAY';
    }

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype(\sprintf('%s[%%s]', $this->getFunctionName()));
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return PHP_INT_MAX; // No upper limit
    }
}
