<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyDays;

class JustifyDaysTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JUSTIFY_DAYS' => JustifyDays::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'adjusts interval so 30-day periods equal months' => "SELECT justify_days('35 days') AS sclr_0 FROM ContainsDates c0_",
            'adjusts interval from entity field' => 'SELECT justify_days(c0_.dateinterval1) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'adjusts interval so 30-day periods equal months' => \sprintf("SELECT JUSTIFY_DAYS('35 days') FROM %s e", ContainsDates::class),
            'adjusts interval from entity field' => \sprintf('SELECT JUSTIFY_DAYS(e.dateinterval1) FROM %s e', ContainsDates::class),
        ];
    }
}
