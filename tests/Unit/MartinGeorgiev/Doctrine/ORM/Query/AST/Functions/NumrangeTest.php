<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange;

class NumrangeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NUMRANGE' => Numrange::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT numrange(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT NUMRANGE(e.decimal1, e.decimal2) FROM %s e', ContainsDecimals::class),
        ];
    }
}
