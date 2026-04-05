<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Network;
use PHPUnit\Framework\Attributes\Test;

class NetworkTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NETWORK' => Network::class,
        ];
    }

    #[Test]
    public function returns_network_address_from_literal(): void
    {
        $dql = "SELECT NETWORK('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.0/24', $result[0]['result']);
    }

    #[Test]
    public function returns_network_address_from_field(): void
    {
        $dql = 'SELECT NETWORK(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.0/24', $result[0]['result']);
    }
}
