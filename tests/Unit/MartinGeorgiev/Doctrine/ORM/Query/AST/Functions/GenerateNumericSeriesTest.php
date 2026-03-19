<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateNumericSeries;

class GenerateNumericSeriesTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GENERATE_NUMERIC_SERIES' => GenerateNumericSeries::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates integer series without explicit step' => 'SELECT generate_series(c0_.integer1, c0_.integer2) AS sclr_0 FROM ContainsNumerics c0_',
            'generates integer series with integer literal step' => 'SELECT generate_series(c0_.integer1, c0_.integer2, 5) AS sclr_0 FROM ContainsNumerics c0_',
            'generates bigint series with integer literal step' => 'SELECT generate_series(c0_.bigint1, c0_.bigint2, 1000) AS sclr_0 FROM ContainsNumerics c0_',
            'generates decimal series without explicit step' => 'SELECT generate_series(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates integer series without explicit step' => \sprintf('SELECT GENERATE_NUMERIC_SERIES(e.integer1, e.integer2) FROM %s e', ContainsNumerics::class),
            'generates integer series with integer literal step' => \sprintf('SELECT GENERATE_NUMERIC_SERIES(e.integer1, e.integer2, 5) FROM %s e', ContainsNumerics::class),
            'generates bigint series with integer literal step' => \sprintf('SELECT GENERATE_NUMERIC_SERIES(e.bigint1, e.bigint2, 1000) FROM %s e', ContainsNumerics::class),
            'generates decimal series without explicit step' => \sprintf('SELECT GENERATE_NUMERIC_SERIES(e.decimal1, e.decimal2) FROM %s e', ContainsNumerics::class),
        ];
    }
}
