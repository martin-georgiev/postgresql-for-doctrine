<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atanh;

class AtanhTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATANH' => Atanh::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ATANH(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ATANH(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ATANH(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ATANH(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
