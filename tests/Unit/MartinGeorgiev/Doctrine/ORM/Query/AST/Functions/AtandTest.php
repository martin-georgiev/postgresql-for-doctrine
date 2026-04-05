<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atand;

class AtandTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAND' => Atand::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ATAND(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ATAND(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ATAND(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ATAND(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
