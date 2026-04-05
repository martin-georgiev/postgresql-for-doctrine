<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asin;

class AsinTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASIN' => Asin::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ASIN(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ASIN(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ASIN(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ASIN(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
