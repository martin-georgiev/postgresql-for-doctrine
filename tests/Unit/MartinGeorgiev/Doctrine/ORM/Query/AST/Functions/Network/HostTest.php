<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Host;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class HostTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HOST' => Host::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT host('192.168.1.5/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT host(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT HOST('192.168.1.5/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT HOST(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
