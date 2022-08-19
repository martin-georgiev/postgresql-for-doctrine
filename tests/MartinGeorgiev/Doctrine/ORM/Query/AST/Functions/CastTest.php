<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsIntegers;

class CastTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT cast(c0_.integer1 AS text) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT CAST(e.integer1, \'text\') FROM %s e', ContainsIntegers::class),
        ];
    }
}
