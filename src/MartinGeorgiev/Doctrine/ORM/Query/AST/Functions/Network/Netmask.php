<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL netmask() function.
 *
 * Returns the network mask for the network, in the same family as the address.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT NETMASK(e.ipAddress) FROM Entity e"
 */
class Netmask extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('netmask(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
