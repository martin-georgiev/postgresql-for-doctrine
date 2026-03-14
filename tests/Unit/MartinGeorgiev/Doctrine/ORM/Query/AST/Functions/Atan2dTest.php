<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2d;

class Atan2dTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAN2D' => Atan2d::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ATAN2D(1, 1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ATAN2D(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ATAN2D(1, 1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ATAN2D(e.decimal1, e.decimal2) FROM %s e', ContainsDecimals::class),
        ];
    }
}
