<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_INSTR().
 *
 * Returns the position within string where the Nth match of the POSIX regular expression pattern occurs,
 * or zero if there is no such match.
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_INSTR(e.text, 'c(.)(..)', 1, 1, 0, 'i') FROM Entity e"
 */
class RegexpInstr extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'regexp_instr';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 6;
    }
}
