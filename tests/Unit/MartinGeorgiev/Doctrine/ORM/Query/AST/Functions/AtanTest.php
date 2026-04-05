<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan;

class AtanTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAN' => Atan::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ATAN(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ATAN(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ATAN(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ATAN(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
