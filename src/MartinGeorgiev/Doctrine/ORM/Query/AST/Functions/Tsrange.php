<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TSRANGE().
 *
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Tsrange extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('tsrange(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
