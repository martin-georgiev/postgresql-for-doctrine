<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Family;
use PHPUnit\Framework\Attributes\Test;

class FamilyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FAMILY' => Family::class,
        ];
    }

    #[Test]
    public function returns_address_family_from_literal(): void
    {
        $dql = "SELECT FAMILY('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result']);
    }

    #[Test]
    public function returns_address_family_from_field(): void
    {
        $dql = 'SELECT FAMILY(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result']);
    }
}
