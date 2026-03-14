<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sind;

class SindTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIND' => Sind::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT SIND(90) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT SIND(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT SIND(90) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT SIND(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
