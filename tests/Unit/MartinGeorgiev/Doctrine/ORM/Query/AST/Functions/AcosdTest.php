<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosd;

class AcosdTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ACOSD' => Acosd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ACOSD(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ACOSD(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ACOSD(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ACOSD(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
