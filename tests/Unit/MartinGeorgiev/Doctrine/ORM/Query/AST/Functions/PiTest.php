<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi;

class PiTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PI' => Pi::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT PI() AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT PI() + c0_.decimal1 AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT PI() FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT PI() + e.decimal1 FROM %s e', ContainsDecimals::class),
        ];
    }
}
