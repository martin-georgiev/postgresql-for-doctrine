<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetSameFamily;
use PHPUnit\Framework\Attributes\Test;

final class InetSameFamilyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INET_SAME_FAMILY' => InetSameFamily::class,
        ];
    }

    #[Test]
    public function returns_whether_the_addresses_share_a_family_from_literals(): void
    {
        $dql = "SELECT INET_SAME_FAMILY('192.168.1.5', '10.0.0.1') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_addresses_share_a_family_from_entity_fields(): void
    {
        $dql = 'SELECT INET_SAME_FAMILY(t.ip, t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
