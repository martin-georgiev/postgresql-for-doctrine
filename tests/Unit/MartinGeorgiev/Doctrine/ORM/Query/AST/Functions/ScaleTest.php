<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Scale;

class ScaleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SCALE' => Scale::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT SCALE(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT SCALE(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT SCALE(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT SCALE(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
