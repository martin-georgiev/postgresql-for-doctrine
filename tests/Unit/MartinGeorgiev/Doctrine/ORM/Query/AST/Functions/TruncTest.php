<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc;

class TruncTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRUNC' => Trunc::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT TRUNC(55.000200) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TRUNC(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TRUNC(c0_.decimal2, 1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TRUNC(55.000200) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TRUNC(e.decimal1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TRUNC(e.decimal2, 1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
