<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL hstore avals() function.
 *
 * Returns an array of an hstore's values.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT HSTORE_AVALS(e.data) FROM Entity e"
 */
class HstoreAvals extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('avals(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
