<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Hostmask;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class HostmaskTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HOSTMASK' => Hostmask::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT hostmask('192.168.1.5/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT hostmask(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT HOSTMASK('192.168.1.5/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT HOSTMASK(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
