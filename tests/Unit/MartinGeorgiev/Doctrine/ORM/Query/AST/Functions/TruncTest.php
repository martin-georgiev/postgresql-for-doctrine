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
            'SELECT TRUNC(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TRUNC(c0_.decimal2, 0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TRUNC(c0_.decimal3, 1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TRUNC(e.decimal1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TRUNC(e.decimal2, 0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TRUNC(e.decimal3, 1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
