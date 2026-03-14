<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL MASKLEN().
 *
 * Returns the netmask length in bits.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT MASKLEN(e.ipAddress) FROM Entity e"
 */
class Masklen extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('masklen(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
