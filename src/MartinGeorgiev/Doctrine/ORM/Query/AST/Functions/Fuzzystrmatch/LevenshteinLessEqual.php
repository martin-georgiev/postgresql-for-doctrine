<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL LEVENSHTEIN_LESS_EQUAL().
 *
 * Accelerated version of the Levenshtein function for use when only small distances are of interest.
 * If the actual distance is less than or equal to max_d, then it returns the correct distance;
 * otherwise it returns some value greater than max_d.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with max distance: "SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 2) FROM Entity e"
 * @example Using it in DQL with custom costs: "SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 1, 2, 3, 5) FROM Entity e"
 */
class LevenshteinLessEqual extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'levenshtein_less_equal';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 6;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        $argumentCount = \count($arguments);
        if ($argumentCount !== 3 && $argumentCount !== 6) {
            throw InvalidArgumentForVariadicFunctionException::unsupportedCombination(
                $this->getFunctionName(),
                $argumentCount,
                'function accepts either 3 arguments (source, target, max_d) or 6 arguments (source, target, ins_cost, del_cost, sub_cost, max_d)'
            );
        }
    }
}

