<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_REPLACE().
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.5
 *
 * @author Colin Doig
 */
class RegexpReplace extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,StringPrimary,StringPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary,StringPrimary,StringPrimary',
            'StringPrimary,StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_replace';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 6;
    }
}
