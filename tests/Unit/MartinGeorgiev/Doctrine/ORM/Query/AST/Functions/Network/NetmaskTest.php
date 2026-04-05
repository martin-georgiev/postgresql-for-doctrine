<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Netmask;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class NetmaskTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NETMASK' => Netmask::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT netmask('192.168.1.5/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT netmask(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT NETMASK('192.168.1.5/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT NETMASK(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
