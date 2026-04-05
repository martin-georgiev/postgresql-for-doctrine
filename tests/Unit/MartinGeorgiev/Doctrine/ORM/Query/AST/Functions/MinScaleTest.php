<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MinScale;

class MinScaleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MIN_SCALE' => MinScale::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT MIN_SCALE(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT MIN_SCALE(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT MIN_SCALE(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT MIN_SCALE(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
