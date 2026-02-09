<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL SPLIT_PART().
 *
 * Splits a string by a delimiter and returns a specified part.
 *
 * @see https://www.postgresql.org/docs/15/functions-string.html
 * @since 2.7
 *
 * @author syl20b
 *
 * @example Using it in DQL: "SELECT SPLIT_PART(e.text, ',', 1) FROM Entity e"
 */
class SplitPart extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('split_part(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
