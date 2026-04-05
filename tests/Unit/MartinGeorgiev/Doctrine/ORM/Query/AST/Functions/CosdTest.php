<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosd;

class CosdTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSD' => Cosd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT COSD(0) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT COSD(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT COSD(0) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT COSD(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
