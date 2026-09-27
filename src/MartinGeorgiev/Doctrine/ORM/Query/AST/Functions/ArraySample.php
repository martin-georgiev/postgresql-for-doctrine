<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_SAMPLE().
 *
 * Returns an array of n randomly selected elements from the input array.
 * The result may contain duplicates if the input array does.
 *
 * @see https://www.postgresql.org/docs/16/functions-array.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_SAMPLE(e.tags, 3) FROM Entity e"
 */
class ArraySample extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_sample(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
