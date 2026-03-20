<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateTimeSeries;

class GenerateTimeSeriesTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GENERATE_TIME_SERIES' => GenerateTimeSeries::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates timestamp series with interval step' => "SELECT generate_series(c0_.datetime1, c0_.datetime2, '1 day') AS sclr_0 FROM ContainsDates c0_",
            'generates timestamptz series with interval step and timezone' => "SELECT generate_series(c0_.datetimetz1, c0_.datetimetz2, '1 hour', 'UTC') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates timestamp series with interval step' => \sprintf("SELECT GENERATE_TIME_SERIES(e.datetime1, e.datetime2, '1 day') FROM %s e", ContainsDates::class),
            'generates timestamptz series with interval step and timezone' => \sprintf("SELECT GENERATE_TIME_SERIES(e.datetimetz1, e.datetimetz2, '1 hour', 'UTC') FROM %s e", ContainsDates::class),
        ];
    }
}
