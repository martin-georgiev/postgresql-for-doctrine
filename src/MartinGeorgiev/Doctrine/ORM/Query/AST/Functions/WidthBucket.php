<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL WIDTH_BUCKET().
 *
 * Assigns a value to a bucket in an equal-width histogram, or to a bucket between the given thresholds.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT WIDTH_BUCKET(e.value, 0.0, 20.0, 4) FROM Entity e"
 * @example Using it in DQL with thresholds: "SELECT WIDTH_BUCKET(e.value, e.thresholds) FROM Entity e"
 */
class WidthBucket extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'WIDTH_BUCKET';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        $argumentCount = \count($arguments);
        if ($argumentCount === 3) {
            throw InvalidArgumentForVariadicFunctionException::unsupportedCombination(
                $this->getFunctionName(),
                $argumentCount,
                'function accepts either 2 arguments (operand, thresholds) or 4 arguments (operand, low, high, count)'
            );
        }
    }
}
