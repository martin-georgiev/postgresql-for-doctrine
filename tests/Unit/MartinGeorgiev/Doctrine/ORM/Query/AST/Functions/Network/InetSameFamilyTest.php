<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetSameFamily;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class InetSameFamilyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INET_SAME_FAMILY' => InetSameFamily::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT inet_same_family('192.168.1.5', '10.0.0.1') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT inet_same_family(c0_.ip, c0_.cidr) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT INET_SAME_FAMILY('192.168.1.5', '10.0.0.1') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT INET_SAME_FAMILY(e.ip, e.cidr) FROM %s e', ContainsNetworks::class),
        ];
    }
}
