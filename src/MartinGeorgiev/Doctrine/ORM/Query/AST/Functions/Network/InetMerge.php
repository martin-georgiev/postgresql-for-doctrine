<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL INET_MERGE().
 *
 * Returns the smallest network that includes both of the given networks.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT INET_MERGE(e.ip1, e.ip2) FROM Entity e"
 */
class InetMerge extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('inet_merge(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
