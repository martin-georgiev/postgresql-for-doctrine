<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyAbove;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class StrictlyAboveTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_ABOVE' => StrictlyAbove::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometry is strictly above' => 'SELECT (c0_.geometry1 |>> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometry is strictly above' => \sprintf('SELECT STRICTLY_ABOVE(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
