<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Network;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class NetworkTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NETWORK' => Network::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT network('192.168.1.5/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT network(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT NETWORK('192.168.1.5/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT NETWORK(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
