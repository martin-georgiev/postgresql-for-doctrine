<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps;

class DateOverlapsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_OVERLAPS' => DateOverlaps::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks non-overlapping date ranges' => "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE (c0_.date1, c0_.date2) OVERLAPS ('2001-12-21', '2001-12-25') = 0",
            'checks overlapping date ranges' => "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE (c0_.date1, c0_.date2) OVERLAPS ('2001-12-21', '2001-12-25') = 1",
            'checks overlapping with null handling true' => "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE (c0_.date1, COALESCE(c0_.date2, CURRENT_DATE)) OVERLAPS ('2001-12-21', '2001-12-25') = 1",
            'checks overlapping with null handling false' => "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE (c0_.date1, COALESCE(c0_.date2, CURRENT_DATE)) OVERLAPS ('2001-12-21', '2001-12-25') = 0",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks non-overlapping date ranges' => \sprintf(
                "SELECT e.date1 FROM %s e WHERE DATE_OVERLAPS(e.date1, e.date2, '2001-12-21', '2001-12-25') = 0",
                ContainsDates::class
            ),
            'checks overlapping date ranges' => \sprintf(
                "SELECT e.date1 FROM %s e WHERE DATE_OVERLAPS(e.date1, e.date2, '2001-12-21', '2001-12-25') = TRUE",
                ContainsDates::class
            ),
            'checks overlapping with null handling true' => \sprintf(
                "SELECT e.date1 FROM %s e WHERE DATE_OVERLAPS(e.date1, COALESCE(e.date2, CURRENT_DATE()), '2001-12-21', '2001-12-25') = 1",
                ContainsDates::class
            ),
            'checks overlapping with null handling false' => \sprintf(
                "SELECT e.date1 FROM %s e WHERE DATE_OVERLAPS(e.date1, COALESCE(e.date2, CURRENT_DATE()), '2001-12-21', '2001-12-25') = FALSE",
                ContainsDates::class
            ),
        ];
    }
}
