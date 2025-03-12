<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_JSONB().
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ToJsonb extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_jsonb(%s)');
        $this->addNodeMapping('NewValue');
    }
}
