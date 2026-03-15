<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Hostmask;
use PHPUnit\Framework\Attributes\Test;

class HostmaskTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HOSTMASK' => Hostmask::class,
        ];
    }

    #[Test]
    public function returns_hostmask_from_literal(): void
    {
        $dql = "SELECT HOSTMASK('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('0.0.0.255', $result[0]['result']);
    }

    #[Test]
    public function returns_hostmask_from_field(): void
    {
        $dql = 'SELECT HOSTMASK(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('0.0.0.255', $result[0]['result']);
    }
}
