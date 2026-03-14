<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosh;

class CoshTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSH' => Cosh::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT COSH(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT COSH(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT COSH(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT COSH(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
