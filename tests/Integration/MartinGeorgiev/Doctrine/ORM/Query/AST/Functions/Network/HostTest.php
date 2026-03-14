<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Host;
use PHPUnit\Framework\Attributes\Test;

class HostTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HOST' => Host::class,
        ];
    }

    #[Test]
    public function returns_host_from_literal(): void
    {
        $dql = "SELECT HOST('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.5', $result[0]['result']);
    }

    #[Test]
    public function returns_host_from_field(): void
    {
        $dql = 'SELECT HOST(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.5', $result[0]['result']);
    }
}
