<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg;

class StringAggTest extends TestCase
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
            // Basic usage
            "SELECT string_agg(c0_.text1, ',') AS sclr_0 FROM ContainsTexts c0_",
            // With concatenation
            "SELECT string_agg(c0_.text1 || c0_.text2, ',') AS sclr_0 FROM ContainsTexts c0_",
            // With DISTINCT
            "SELECT string_agg(DISTINCT c0_.text1, ',') AS sclr_0 FROM ContainsTexts c0_",
            // With DISTINCT and concatenation
            "SELECT string_agg(DISTINCT c0_.text1 || c0_.text2, ',') AS sclr_0 FROM ContainsTexts c0_",
            // With ORDER BY
            "SELECT string_agg(c0_.text1, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
            "SELECT string_agg(c0_.text1, ',' ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_",
            // With DISTINCT and ORDER BY
            "SELECT string_agg(DISTINCT c0_.text1, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
            // With concatenation, DISTINCT and ORDER BY
            "SELECT string_agg(DISTINCT c0_.text1 || c0_.text2, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
            // With multiple ORDER BY columns
            "SELECT string_agg(c0_.text1, ',' ORDER BY c0_.text1 ASC, c0_.text2 DESC) AS sclr_0 FROM ContainsTexts c0_",
            // With different delimiter
            "SELECT string_agg(c0_.text1, ' | ') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT STRING_AGG(e.text1, ',') FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(CONCAT(e.text1, e.text2), ',') FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(DISTINCT e.text1, ',') FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(DISTINCT CONCAT(e.text1, e.text2), ',') FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(e.text1, ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(e.text1, ',' ORDER BY e.text1 DESC) FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(DISTINCT e.text1, ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(DISTINCT CONCAT(e.text1, e.text2), ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(e.text1, ',' ORDER BY e.text1 ASC, e.text2 DESC) FROM %s e", ContainsTexts::class),
            \sprintf("SELECT STRING_AGG(e.text1, ' | ') FROM %s e", ContainsTexts::class),
        ];
    }
}
