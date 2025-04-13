<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

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
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('regexp_instr(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2 || $argumentCount > 6) {
            throw InvalidArgumentForVariadicFunctionException::between('regexp_instr', 2, 6);
        }
    }
}
