<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of pgvector inner product (dot product) function.
 *
 * Returns the inner product of two vectors.
 * Wraps the inner_product(vector, vector) SQL function.
 *
 * Note: The pgvector <#> operator returns the negative inner product. Use this function
 * to get the actual inner product value.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InnerProduct extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('inner_product(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
