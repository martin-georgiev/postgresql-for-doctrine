<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cos;

class CosTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COS' => Cos::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT COS(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT COS(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT COS(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT COS(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
