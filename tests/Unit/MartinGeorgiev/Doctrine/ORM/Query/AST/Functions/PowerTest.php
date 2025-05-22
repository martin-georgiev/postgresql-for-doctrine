<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power;

class PowerTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'POWER' => Power::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT POWER(c0_.decimal1, 2) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT POWER(c0_.decimal2, c0_.decimal3) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT POWER(2, 3) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT POWER(e.decimal1, 2) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT POWER(e.decimal2, e.decimal3) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT POWER(2, 3) FROM %s e', ContainsDecimals::class),
        ];
    }
}
