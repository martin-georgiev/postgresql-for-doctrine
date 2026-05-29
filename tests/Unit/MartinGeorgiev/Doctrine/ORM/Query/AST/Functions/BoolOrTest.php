<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolOr;

class BoolOrTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BOOL_OR' => BoolOr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'aggregates boolean field' => 'SELECT bool_or(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'aggregates boolean field' => \sprintf('SELECT BOOL_OR(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
