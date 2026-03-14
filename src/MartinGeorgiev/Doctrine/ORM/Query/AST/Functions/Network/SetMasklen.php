<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL set_masklen() function.
 *
 * Sets the netmask length for an inet value, returning the modified address with the new prefix length.
 *
 * @see https://www.postgresql.org/docs/18/functions-net.html
 * @since 4.4
 *
 *
 * @example Using it in DQL: "SELECT SET_MASKLEN(e.ipAddress, 16) FROM Entity e"
 */
class SetMasklen extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('set_masklen(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
