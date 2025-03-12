<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ILIKE().
 *
 * @see https://www.postgresql.org/docs/9.3/functions-matching.html
 * @since 1.1
 *
 * @author llaakkkk <lenakirichokv@gmail.com>
 */
class Ilike extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s ilike %s');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
