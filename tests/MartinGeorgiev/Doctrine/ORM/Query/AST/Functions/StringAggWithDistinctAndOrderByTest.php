<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class StringAggWithDistinctAndOrderByTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRING_AGG' => StringAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT string_agg(distinct c0_.text1 || c0_.text2, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT STRING_AGG(DISTINCT CONCAT(e.text1, e.text2), ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
        ];
    }
}
