<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL hstore hstore_to_json_loose() function.
 *
 * Converts an hstore to a json value, attempting to distinguish numerical and boolean values.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT HSTORE_TO_JSON_LOOSE(e.data) FROM Entity e"
 */
class HstoreToJsonLoose extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('hstore_to_json_loose(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
