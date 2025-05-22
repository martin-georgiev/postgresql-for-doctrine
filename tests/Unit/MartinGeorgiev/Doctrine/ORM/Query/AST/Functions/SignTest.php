<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign;

class SignTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIGN' => Sign::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT SIGN(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT SIGN(c0_.decimal2) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT SIGN(c0_.decimal3) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT SIGN(e.decimal1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT SIGN(e.decimal2) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT SIGN(e.decimal3) FROM %s e', ContainsDecimals::class),
        ];
    }
}
