<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;

class LogTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LOG' => Log::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT LOG(8) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT LOG(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT LOG(10, c0_.decimal2) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT LOG(8) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT LOG(e.decimal1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT LOG(10, e.decimal2) FROM %s e', ContainsDecimals::class),
        ];
    }
}
