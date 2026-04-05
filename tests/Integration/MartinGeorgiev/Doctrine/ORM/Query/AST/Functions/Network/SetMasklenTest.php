<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\SetMasklen;
use PHPUnit\Framework\Attributes\Test;

class SetMasklenTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SET_MASKLEN' => SetMasklen::class,
        ];
    }

    #[Test]
    public function sets_mask_length_from_literal(): void
    {
        $dql = "SELECT SET_MASKLEN('192.168.1.5/24', 16) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.5/16', $result[0]['result']);
    }

    #[Test]
    public function sets_mask_length_from_field(): void
    {
        $dql = 'SELECT SET_MASKLEN(t.ip, 16) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.5/16', $result[0]['result']);
    }
}
