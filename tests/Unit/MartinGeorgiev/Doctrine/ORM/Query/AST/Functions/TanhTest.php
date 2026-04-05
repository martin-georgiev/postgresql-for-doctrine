<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tanh;

class TanhTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TANH' => Tanh::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT TANH(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TANH(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TANH(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TANH(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
