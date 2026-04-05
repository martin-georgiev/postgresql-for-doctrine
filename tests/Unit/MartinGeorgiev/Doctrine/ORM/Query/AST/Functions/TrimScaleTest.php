<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimScale;

class TrimScaleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRIM_SCALE' => TrimScale::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT TRIM_SCALE(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TRIM_SCALE(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TRIM_SCALE(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TRIM_SCALE(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
