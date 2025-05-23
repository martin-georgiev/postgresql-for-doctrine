<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln;

class LnTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LN' => Ln::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT LN(12) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT LN(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT LN(12) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT LN(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
