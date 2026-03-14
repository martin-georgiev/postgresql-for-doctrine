<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tand;

class TandTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TAND' => Tand::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT TAND(45) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT TAND(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TAND(45) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT TAND(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
