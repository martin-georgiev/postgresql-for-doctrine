<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSON_AGG().
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonAgg extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('json_agg(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
