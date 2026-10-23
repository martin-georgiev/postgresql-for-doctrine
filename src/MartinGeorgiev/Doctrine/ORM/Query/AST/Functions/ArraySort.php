<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL ARRAY_SORT().
 *
 * Sorts the elements of an array, optionally in descending order and with NULLs first.
 *
 * @see https://www.postgresql.org/docs/18/functions-array.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_SORT(e.array) FROM Entity e"
 * @example Using it in DQL with descending and nulls first: "SELECT ARRAY_SORT(e.array, 'true', 'false') FROM Entity e"
 */
class ArraySort extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getFunctionName(): string
    {
        return 'array_sort';
    }

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary',
        ];
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        foreach (\array_slice($arguments, 1) as $node) {
            $this->validateBoolean($node, $this->getFunctionName());
        }
    }
}
