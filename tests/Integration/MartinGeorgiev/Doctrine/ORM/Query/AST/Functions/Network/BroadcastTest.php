<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Broadcast;
use PHPUnit\Framework\Attributes\Test;

class BroadcastTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BROADCAST' => Broadcast::class,
        ];
    }

    #[Test]
    public function returns_broadcast_address_from_literal(): void
    {
        $dql = "SELECT BROADCAST('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.255/24', $result[0]['result']);
    }

    #[Test]
    public function returns_broadcast_address_from_field(): void
    {
        $dql = 'SELECT BROADCAST(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.255/24', $result[0]['result']);
    }
}
