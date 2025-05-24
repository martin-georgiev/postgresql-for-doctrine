<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians;

class RadiansTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RADIANS' => Radians::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT RADIANS(22) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT RADIANS(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT RADIANS(22) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT RADIANS(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
