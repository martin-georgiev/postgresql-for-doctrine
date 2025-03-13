<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL check if a given JSON value will return value for teh left-side JSONB (using @?).
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ReturnsValueForJsonValue extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s @?? %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
