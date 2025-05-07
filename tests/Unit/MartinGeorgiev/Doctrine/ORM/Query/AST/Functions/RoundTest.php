<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round;

class RoundTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROUND' => Round::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ROUND(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ROUND(c0_.decimal2, 0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ROUND(c0_.decimal3, 1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ROUND(e.decimal1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ROUND(e.decimal2, 0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ROUND(e.decimal3, 1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
