<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Abbrev;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class AbbrevTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ABBREV' => Abbrev::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT abbrev('192.168.1.0/24') AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT abbrev(c0_.ip) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT ABBREV('192.168.1.0/24') FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT ABBREV(e.ip) FROM %s e', ContainsNetworks::class),
        ];
    }
}
