<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL REGEXP_SUBSTR().
 *
 * Returns the substring within string that matches the Nth occurrence of the POSIX regular expression pattern,
 * or NULL if there is no such match.
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REGEXP_SUBSTR(e.text, 'c(.)(..)', 1, 1, 'i') FROM Entity e"
 */
class RegexpSubstr extends BaseVariadicFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('regexp_substr(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2 || $argumentCount > 5) {
            throw InvalidArgumentForVariadicFunctionException::between('regexp_substr', 2, 5);
        }
    }
}
