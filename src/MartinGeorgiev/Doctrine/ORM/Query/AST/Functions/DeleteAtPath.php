<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql deletion of a field at the specified path (using #-).
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DeleteAtPath extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('(%s #- %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
