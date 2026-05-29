<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyHours;

class JustifyHoursTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JUSTIFY_HOURS' => JustifyHours::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'adjusts interval so 24-hour periods equal days' => "SELECT justify_hours('27 hours') AS sclr_0 FROM ContainsDates c0_",
            'adjusts interval from entity field' => 'SELECT justify_hours(c0_.dateinterval1) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'adjusts interval so 24-hour periods equal days' => \sprintf("SELECT JUSTIFY_HOURS('27 hours') FROM %s e", ContainsDates::class),
            'adjusts interval from entity field' => \sprintf('SELECT JUSTIFY_HOURS(e.dateinterval1) FROM %s e', ContainsDates::class),
        ];
    }
}
