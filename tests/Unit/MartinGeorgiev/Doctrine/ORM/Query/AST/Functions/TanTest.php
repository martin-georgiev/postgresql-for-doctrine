<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tan;

class TanTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TAN' => Tan::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT TAN(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TAN(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TAN(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TAN(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
