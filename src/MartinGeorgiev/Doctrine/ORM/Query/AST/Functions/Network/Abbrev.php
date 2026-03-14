<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL abbrev() function.
 *
 * Returns the abbreviated text form of the IP address or CIDR block, omitting the netmask if it is the default.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT ABBREV(e.ipAddress) FROM Entity e"
 */
class Abbrev extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('abbrev(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
