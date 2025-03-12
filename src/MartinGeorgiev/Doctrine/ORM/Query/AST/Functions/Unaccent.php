<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL UNACCENT.
 *
 * @see http://www.postgresql.org/docs/current/static/unaccent.html
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class Unaccent extends BaseVariadicFunction
{
    protected string $commonNodeMapping = 'StringPrimary';

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('unaccent(%s)');
    }
}
