<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL HOST().
 *
 * Returns the IP address as text, ignoring the netmask.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT HOST(e.ipAddress) FROM Entity e"
 */
class Host extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('host(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
