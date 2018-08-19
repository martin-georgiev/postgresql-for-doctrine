<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql TO_TSQUERY()
 * @see https://www.postgresql.org/docs/9.4/static/textsearch-controls.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ToTsquery extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('to_tsquery(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
