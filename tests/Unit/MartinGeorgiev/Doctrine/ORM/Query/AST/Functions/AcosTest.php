<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acos;

class AcosTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOS' => Acos::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ACOS(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ACOS(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ACOS(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ACOS(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
