<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CHR().
 *
 * Returns the character with the given code point.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CHR(65) FROM Entity e"
 */
class Chr extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('chr(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
