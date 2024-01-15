<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql check if any text on the right side exists on the left-side JSONB (using ?|).
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class AnyOnTheRightExistsOnTheLeft extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('(%s ??| %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
