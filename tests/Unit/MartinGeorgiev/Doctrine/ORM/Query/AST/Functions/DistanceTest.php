<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsPoints;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance;

class DistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DISTANCE' => Distance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.point1 <@> '(2.320041, 48.858889)') AS sclr_0 FROM ContainsPoints c0_",
            'SELECT (c0_.point1 <@> c0_.point2) AS sclr_0 FROM ContainsPoints c0_',
            "SELECT ('(1.0, 1.0)' <@> '(2.0, 2.0)') AS sclr_0 FROM ContainsPoints c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT DISTANCE(e.point1, '(2.320041, 48.858889)') FROM %s e", ContainsPoints::class),
            \sprintf('SELECT DISTANCE(e.point1, e.point2) FROM %s e', ContainsPoints::class),
            \sprintf("SELECT DISTANCE('(1.0, 1.0)', '(2.0, 2.0)') FROM %s e", ContainsPoints::class),
        ];
    }
}
