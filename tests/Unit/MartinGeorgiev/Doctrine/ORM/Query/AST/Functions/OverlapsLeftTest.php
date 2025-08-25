<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OverlapsLeft;

class OverlapsLeftTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_LEFT' => OverlapsLeft::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometry overlaps or is to the left' => 'SELECT (c0_.geometry1 &< c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'checks if geometry overlaps or is to the left of literal' => "SELECT (c0_.geometry1 &< 'POINT(1 2)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometry overlaps or is to the left' => \sprintf('SELECT OVERLAPS_LEFT(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'checks if geometry overlaps or is to the left of literal' => \sprintf("SELECT OVERLAPS_LEFT(e.geometry1, 'POINT(1 2)') FROM %s e", ContainsGeometries::class),
        ];
    }
}
