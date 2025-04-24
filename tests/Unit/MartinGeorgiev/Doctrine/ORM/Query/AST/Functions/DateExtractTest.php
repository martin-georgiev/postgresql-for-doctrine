<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract;

class DateExtractTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_EXTRACT' => DateExtract::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT EXTRACT('DAY' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            "SELECT EXTRACT('MONTH' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            "SELECT EXTRACT('YEAR' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE EXTRACT('DAY' FROM c0_.date1) = 7",
            "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE EXTRACT('MONTH' FROM c0_.date1) = 12",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT DATE_EXTRACT('DAY', e.date1) FROM %s e", ContainsDates::class),
            \sprintf("SELECT DATE_EXTRACT('MONTH', e.date1) FROM %s e", ContainsDates::class),
            \sprintf("SELECT DATE_EXTRACT('YEAR', e.date1) FROM %s e", ContainsDates::class),
            \sprintf("SELECT e.date1 FROM %s e WHERE DATE_EXTRACT('DAY', e.date1) = 7", ContainsDates::class),
            \sprintf("SELECT e.date1 FROM %s e WHERE DATE_EXTRACT('MONTH', e.date1) = 12", ContainsDates::class),
        ];
    }
}
