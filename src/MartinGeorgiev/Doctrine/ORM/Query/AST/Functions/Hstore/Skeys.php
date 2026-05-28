<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL hstore skeys() function.
 *
 * Returns an hstore's keys as a set.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT HSTORE_SKEYS(e.data) FROM Entity e"
 */
class Skeys extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('skeys(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
