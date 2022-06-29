<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsDates;

class DateExtractTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EXTRACT' => DateExtract::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT EXTRACT('DAY' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE EXTRACT('DAY' FROM c0_.date1) = 1",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT EXTRACT('DAY', e.date1) FROM %s e", ContainsDates::class),
            \sprintf("SELECT e.date1 FROM %s e WHERE EXTRACT('DAY', e.date1) = 1", ContainsDates::class),
        ];
    }
}
