<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erfc;

class ErfcTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ERFC' => Erfc::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ERFC(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ERFC(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ERFC(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ERFC(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
