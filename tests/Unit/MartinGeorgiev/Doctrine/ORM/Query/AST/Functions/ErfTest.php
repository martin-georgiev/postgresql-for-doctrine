<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erf;

class ErfTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ERF' => Erf::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ERF(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ERF(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ERF(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ERF(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
