<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL NETWORK().
 *
 * Returns the network address for the IP address, zeroing out the host bits of the address.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 * @example Using it in DQL: "SELECT NETWORK(e.ipAddress) FROM Entity e"
 */
class Network extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('network(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
