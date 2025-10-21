<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSON_STRIP_NULLS().
 *
 * Supports optional second parameter (PostgreSQL 18+) to control null stripping from arrays.
 *
 * @see https://www.postgresql.org/docs/18/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonStripNulls extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getFunctionName(): string
    {
        return 'json_strip_nulls';
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
        return 2;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        if (\count($arguments) === 2) {
            $this->validateBoolean($arguments[1], $this->getFunctionName());
        }
    }
}
