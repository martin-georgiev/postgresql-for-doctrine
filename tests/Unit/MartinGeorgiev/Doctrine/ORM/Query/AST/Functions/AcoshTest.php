<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosh;

class AcoshTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOSH' => Acosh::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ACOSH(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ACOSH(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ACOSH(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ACOSH(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
