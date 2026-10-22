<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL RANDOM().
 *
 * Returns a random value between 0.0 and 1.0, or between a minimum and a maximum.
 * The bounds can be integers or numerics (PostgreSQL 17+), or dates or timestamps (PostgreSQL 19+).
 *
 * @see https://www.postgresql.org/docs/19/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RANDOM() FROM Entity e"
 * @example Using it in DQL with bounds: "SELECT RANDOM(1, 10) FROM Entity e"
 */
class Random extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'RANDOM';
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }

    protected function getMinArgumentCount(): int
    {
        return 0;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        $argumentCount = \count($arguments);
        if ($argumentCount === 1) {
            throw InvalidArgumentForVariadicFunctionException::unsupportedCombination(
                $this->getFunctionName(),
                $argumentCount,
                'function accepts either no arguments or 2 arguments (min, max)'
            );
        }
    }
}
