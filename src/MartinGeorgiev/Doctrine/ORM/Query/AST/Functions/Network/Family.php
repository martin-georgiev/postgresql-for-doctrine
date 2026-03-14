<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL family() function.
 *
 * Returns the address family of the IP address: 4 for IPv4, 6 for IPv6.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT FAMILY(e.ipAddress) FROM Entity e"
 */
class Family extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('family(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
