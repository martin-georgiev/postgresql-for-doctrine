<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\SetMasklen;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class SetMasklenTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SET_MASKLEN' => SetMasklen::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT set_masklen('192.168.1.5/24', 16) AS sclr_0 FROM ContainsNetworks c0_",
            'SELECT set_masklen(c0_.ip, 16) AS sclr_0 FROM ContainsNetworks c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT SET_MASKLEN('192.168.1.5/24', 16) FROM %s e", ContainsNetworks::class),
            \sprintf('SELECT SET_MASKLEN(e.ip, 16) FROM %s e', ContainsNetworks::class),
        ];
    }
}
