<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone;

class AtTimeZoneTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'AT_TIME_ZONE' => AtTimeZone::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts timestamp to given timezone' => "SELECT c0_.datetime1 AT TIME ZONE 'UTC' AS sclr_0 FROM ContainsDates c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts timestamp to given timezone' => \sprintf("SELECT AT_TIME_ZONE(e.datetime1, 'UTC') FROM %s e", ContainsDates::class),
        ];
    }
}
