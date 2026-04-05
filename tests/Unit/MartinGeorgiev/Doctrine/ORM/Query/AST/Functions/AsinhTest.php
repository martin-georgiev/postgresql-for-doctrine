<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asinh;

class AsinhTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASINH' => Asinh::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ASINH(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ASINH(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ASINH(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ASINH(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
