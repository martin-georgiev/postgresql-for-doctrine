<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL UUIDV4().
 *
 * Generates a random UUID v4.
 *
 * @see https://www.postgresql.org/docs/18/functions-uuid.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT UUIDV4() FROM Entity e"
 */
class Uuidv4 extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('uuidv4()');
    }
}
