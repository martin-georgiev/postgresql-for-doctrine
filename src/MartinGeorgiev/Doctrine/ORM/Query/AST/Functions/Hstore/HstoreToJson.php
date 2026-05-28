<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL hstore HSTORE_TO_JSON().
 *
 * Converts an hstore to a json value.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT HSTORE_TO_JSON(e.data) FROM Entity e"
 */
class HstoreToJson extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('hstore_to_json(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
