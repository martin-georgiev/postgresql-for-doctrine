<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith;

class StartsWithTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STARTS_WITH' => StartsWith::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (STARTS_WITH(c0_.text1, 'TEST')) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT STARTS_WITH(e.text1,'TEST') FROM %s e", ContainsTexts::class),
        ];
    }
}
