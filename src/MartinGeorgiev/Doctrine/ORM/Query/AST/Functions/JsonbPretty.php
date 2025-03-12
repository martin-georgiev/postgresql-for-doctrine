<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_PRETTY().
 *
 * @see https://www.postgresql.org/docs/13/functions-json.html
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbPretty extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_pretty(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
