<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL HOSTMASK().
 *
 * Returns the host mask for the network (the complement of the netmask).
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT HOSTMASK(e.ipAddress) FROM Entity e"
 */
class Hostmask extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('hostmask(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
