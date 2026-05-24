<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL hstore svals() function.
 *
 * Returns an hstore's values as a set.
 *
 * @see https://www.postgresql.org/docs/18/hstore.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT HSTORE_SVALS(e.data) FROM Entity e"
 */
class HstoreSvals extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('svals(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
