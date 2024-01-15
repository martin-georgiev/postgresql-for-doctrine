<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql check if the right-side text exists on the left-side JSONB (using ?|).
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TheRightExistsOnTheLeft extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('(%s ?? %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
