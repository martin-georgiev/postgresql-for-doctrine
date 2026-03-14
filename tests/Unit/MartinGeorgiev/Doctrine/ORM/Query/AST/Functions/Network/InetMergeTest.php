<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetMerge;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class InetMergeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INET_MERGE' => InetMerge::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT inet_merge('192.168.1.5/24', '10.0.0.1/8') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT inet_merge(c0_.ip, c0_.cidr_block) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT INET_MERGE('192.168.1.5/24', '10.0.0.1/8') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT INET_MERGE(e.ip, e.cidr) FROM %s e', ContainsNetworks::class),
        ];
    }
}
