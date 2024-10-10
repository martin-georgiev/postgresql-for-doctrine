<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql SPLIT_PART().
 *
 * @see https://www.postgresql.org/docs/15/functions-string.html
 * @since 2.7.0
 *
 * @author syl20b
 */
class SplitPart extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('split_part(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
