<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL UUIDV7().
 *
 * @see https://www.postgresql.org/docs/18/functions-uuid.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Uuidv7 extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('uuidv7()');
    }
}
