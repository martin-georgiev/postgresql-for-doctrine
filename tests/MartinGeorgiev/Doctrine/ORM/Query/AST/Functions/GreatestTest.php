<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseComparisonFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsIntegers;

class GreatestTest extends BaseComparisonFunctionTest
{
    protected function createFixture(): BaseComparisonFunction
    {
        return new Greatest('greatest');
    }

    protected function getStringFunctions(): array
    {
        return [
            'GREATEST' => Greatest::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT greatest(c0_.integer1, c0_.integer2, c0_.integer3) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT GREATEST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsIntegers::class),
        ];
    }
}
