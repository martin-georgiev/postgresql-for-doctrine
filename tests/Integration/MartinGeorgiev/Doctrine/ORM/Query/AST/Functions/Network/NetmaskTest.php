<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Netmask;
use PHPUnit\Framework\Attributes\Test;

class NetmaskTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NETMASK' => Netmask::class,
        ];
    }

    #[Test]
    public function returns_netmask_from_literal(): void
    {
        $dql = "SELECT NETMASK('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('255.255.255.0', $result[0]['result']);
    }

    #[Test]
    public function returns_netmask_from_field(): void
    {
        $dql = 'SELECT NETMASK(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('255.255.255.0', $result[0]['result']);
    }
}
