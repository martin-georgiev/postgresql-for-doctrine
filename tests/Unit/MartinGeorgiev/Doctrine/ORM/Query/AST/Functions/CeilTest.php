<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil;

class CeilTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CEIL' => Ceil::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT CEIL(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT CEIL(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
