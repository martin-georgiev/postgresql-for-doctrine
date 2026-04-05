<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Factorial;

class FactorialTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FACTORIAL' => Factorial::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT FACTORIAL(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT FACTORIAL(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT FACTORIAL(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT FACTORIAL(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
