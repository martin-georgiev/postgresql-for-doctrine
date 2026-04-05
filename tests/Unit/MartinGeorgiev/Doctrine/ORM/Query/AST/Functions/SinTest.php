<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sin;

class SinTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIN' => Sin::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT SIN(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT SIN(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT SIN(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT SIN(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
