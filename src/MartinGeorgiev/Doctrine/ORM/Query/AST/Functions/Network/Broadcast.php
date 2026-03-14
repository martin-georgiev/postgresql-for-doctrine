<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL BROADCAST().
 *
 * Returns the broadcast address for the network.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT BROADCAST(e.ipAddress) FROM Entity e"
 */
class Broadcast extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('broadcast(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
