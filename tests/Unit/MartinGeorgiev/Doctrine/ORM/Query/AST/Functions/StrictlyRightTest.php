<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrictlyRight;

class StrictlyRightTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_RIGHT' => StrictlyRight::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if geometry is strictly to the right' => 'SELECT (c0_.geometry1 >> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if geometry is strictly to the right' => \sprintf('SELECT STRICTLY_RIGHT(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
