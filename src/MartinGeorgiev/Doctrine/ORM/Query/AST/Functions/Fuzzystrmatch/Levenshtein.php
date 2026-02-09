<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL LEVENSHTEIN().
 *
 * Computes the Levenshtein distance between two strings.
 * The Levenshtein distance is the minimum number of single-character edits
 * (insertions, deletions, or substitutions) required to change one string into the other.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with two strings: "SELECT LEVENSHTEIN(e.text1, e.text2) FROM Entity e"
 * @example Using it in DQL with custom costs: "SELECT LEVENSHTEIN(e.text1, e.text2, 1, 2, 3) FROM Entity e"
 */
class Levenshtein extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'levenshtein';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 5;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        $argumentCount = \count($arguments);
        if ($argumentCount !== 2 && $argumentCount !== 5) {
            throw InvalidArgumentForVariadicFunctionException::unsupportedCombination(
                $this->getFunctionName(),
                $argumentCount,
                'function accepts either 2 arguments (source, target) or 5 arguments (source, target, ins_cost, del_cost, sub_cost)'
            );
        }
    }
}

