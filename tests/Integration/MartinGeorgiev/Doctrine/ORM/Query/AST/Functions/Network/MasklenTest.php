<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Masklen;
use PHPUnit\Framework\Attributes\Test;

class MasklenTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MASKLEN' => Masklen::class,
        ];
    }

    #[Test]
    public function returns_mask_length_from_literal(): void
    {
        $dql = "SELECT MASKLEN('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(24, $result[0]['result']);
    }

    #[Test]
    public function returns_mask_length_from_field(): void
    {
        $dql = 'SELECT MASKLEN(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(24, $result[0]['result']);
    }
}
