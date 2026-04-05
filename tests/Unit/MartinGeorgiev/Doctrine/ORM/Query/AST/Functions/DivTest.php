<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Div;

class DivTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DIV' => Div::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT DIV(1, 1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT DIV(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DIV(1, 1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT DIV(e.decimal1, e.decimal2) FROM %s e', ContainsDecimals::class),
        ];
    }
}
