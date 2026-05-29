<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyInterval;

class JustifyIntervalTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JUSTIFY_INTERVAL' => JustifyInterval::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'adjusts interval using justify_days and justify_hours' => "SELECT justify_interval('1 mon -1 hour') AS sclr_0 FROM ContainsDates c0_",
            'adjusts interval from entity field' => 'SELECT justify_interval(c0_.dateinterval1) AS sclr_0 FROM ContainsDates c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'adjusts interval using justify_days and justify_hours' => \sprintf("SELECT JUSTIFY_INTERVAL('1 mon -1 hour') FROM %s e", ContainsDates::class),
            'adjusts interval from entity field' => \sprintf('SELECT JUSTIFY_INTERVAL(e.dateinterval1) FROM %s e', ContainsDates::class),
        ];
    }
}
