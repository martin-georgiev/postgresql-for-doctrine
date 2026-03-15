<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetMerge;
use PHPUnit\Framework\Attributes\Test;

class InetMergeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INET_MERGE' => InetMerge::class,
        ];
    }

    #[Test]
    public function returns_smallest_enclosing_network_from_literals(): void
    {
        $dql = "SELECT INET_MERGE('192.168.1.5/24', '10.0.0.1/8') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('0.0.0.0/0', $result[0]['result']);
    }

    #[Test]
    public function returns_smallest_enclosing_network_from_fields(): void
    {
        $dql = 'SELECT INET_MERGE(t.ip, t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.0/24', $result[0]['result']);
    }
}
