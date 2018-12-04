<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ILIKE()
 * @see    https://www.postgresql.org/docs/9.3/functions-matching.html
 * @since  0.4
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ILike extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('%s ilike %s');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
