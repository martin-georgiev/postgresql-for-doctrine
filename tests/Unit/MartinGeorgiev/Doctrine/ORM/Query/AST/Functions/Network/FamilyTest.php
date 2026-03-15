<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Family;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class FamilyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FAMILY' => Family::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT family('192.168.1.5/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT family(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT FAMILY('192.168.1.5/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT FAMILY(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
