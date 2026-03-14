<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cot;

class CotTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COT' => Cot::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT COT(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT COT(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT COT(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT COT(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
