<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL hstore AKEYS().
 *
 * Returns an array of an hstore's keys.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT HSTORE_AKEYS(e.data) FROM Entity e"
 */
class Akeys extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('akeys(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
