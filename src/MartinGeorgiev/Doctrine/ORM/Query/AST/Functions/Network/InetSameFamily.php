<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL INET_SAME_FAMILY().
 *
 * Returns true if the two addresses belong to the same IP address family (both IPv4 or both IPv6).
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 * @example Using it in DQL with boolean comparison: "WHERE INET_SAME_FAMILY(e.ip1, e.ip2) = TRUE"
 */
class InetSameFamily extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('inet_same_family(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
