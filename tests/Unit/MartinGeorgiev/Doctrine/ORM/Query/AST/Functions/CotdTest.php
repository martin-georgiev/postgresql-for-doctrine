<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cotd;

class CotdTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COTD' => Cotd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT COTD(45) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT COTD(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT COTD(45) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT COTD(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
