<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Masklen;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class MasklenTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MASKLEN' => Masklen::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT masklen('192.168.1.5/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT masklen(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT MASKLEN('192.168.1.5/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT MASKLEN(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
