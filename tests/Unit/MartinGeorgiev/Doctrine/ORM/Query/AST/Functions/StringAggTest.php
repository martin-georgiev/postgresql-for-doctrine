<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'basic usage' => "SELECT string_agg(c0_.text1, ',') AS sclr_0 FROM ContainsTexts c0_",
            'with concatenation' => "SELECT string_agg(c0_.text1 || c0_.text2, ',') AS sclr_0 FROM ContainsTexts c0_",
            'with DISTINCT' => "SELECT string_agg(DISTINCT c0_.text1, ',') AS sclr_0 FROM ContainsTexts c0_",
            'with DISTINCT and concatenation' => "SELECT string_agg(DISTINCT c0_.text1 || c0_.text2, ',') AS sclr_0 FROM ContainsTexts c0_",
            'with ORDER BY' => "SELECT string_agg(c0_.text1, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
            'with ORDER BY DESC' => "SELECT string_agg(c0_.text1, ',' ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_",
            'with DISTINCT and ORDER BY' => "SELECT string_agg(DISTINCT c0_.text1, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
            'with concatenation, DISTINCT and ORDER BY' => "SELECT string_agg(DISTINCT c0_.text1 || c0_.text2, ',' ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_",
            'with multiple ORDER BY columns' => "SELECT string_agg(c0_.text1, ',' ORDER BY c0_.text1 ASC, c0_.text2 DESC) AS sclr_0 FROM ContainsTexts c0_",
            'with different delimiter' => "SELECT string_agg(c0_.text1, ' | ') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf("SELECT STRING_AGG(e.text1, ',') FROM %s e", ContainsTexts::class),
            'with concatenation' => \sprintf("SELECT STRING_AGG(CONCAT(e.text1, e.text2), ',') FROM %s e", ContainsTexts::class),
            'with DISTINCT' => \sprintf("SELECT STRING_AGG(DISTINCT e.text1, ',') FROM %s e", ContainsTexts::class),
            'with DISTINCT and concatenation' => \sprintf("SELECT STRING_AGG(DISTINCT CONCAT(e.text1, e.text2), ',') FROM %s e", ContainsTexts::class),
            'with ORDER BY' => \sprintf("SELECT STRING_AGG(e.text1, ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
            'with ORDER BY DESC' => \sprintf("SELECT STRING_AGG(e.text1, ',' ORDER BY e.text1 DESC) FROM %s e", ContainsTexts::class),
            'with DISTINCT and ORDER BY' => \sprintf("SELECT STRING_AGG(DISTINCT e.text1, ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
            'with concatenation, DISTINCT and ORDER BY' => \sprintf("SELECT STRING_AGG(DISTINCT CONCAT(e.text1, e.text2), ',' ORDER BY e.text1) FROM %s e", ContainsTexts::class),
            'with multiple ORDER BY columns' => \sprintf("SELECT STRING_AGG(e.text1, ',' ORDER BY e.text1 ASC, e.text2 DESC) FROM %s e", ContainsTexts::class),
            'with different delimiter' => \sprintf("SELECT STRING_AGG(e.text1, ' | ') FROM %s e", ContainsTexts::class),
        ];
    }
}
