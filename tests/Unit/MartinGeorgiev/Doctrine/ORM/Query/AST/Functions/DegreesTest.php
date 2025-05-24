<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees;

class DegreesTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DEGREES' => Degrees::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT DEGREES(33) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT DEGREES(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DEGREES(33) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT DEGREES(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
