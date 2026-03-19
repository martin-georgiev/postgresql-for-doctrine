<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateSeries;

class GenerateSeriesTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GENERATE_SERIES' => GenerateSeries::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates series between two dates' => 'SELECT generate_series(c0_.date1, c0_.date2) AS sclr_0 FROM ContainsDates c0_',
            'generates series between two dates with step' => "SELECT generate_series(c0_.date1, c0_.date2, '1 day') AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates series between two dates' => \sprintf('SELECT GENERATE_SERIES(e.date1, e.date2) FROM %s e', ContainsDates::class),
            'generates series between two dates with step' => \sprintf("SELECT GENERATE_SERIES(e.date1, e.date2, '1 day') FROM %s e", ContainsDates::class),
        ];
    }
}
