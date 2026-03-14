<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sinh;

class SinhTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SINH' => Sinh::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT SINH(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT SINH(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT SINH(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT SINH(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
