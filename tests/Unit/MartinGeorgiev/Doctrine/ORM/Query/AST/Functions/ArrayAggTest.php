<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;

class ArrayAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic usage' => 'SELECT array_agg(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with concatenation' => 'SELECT array_agg(c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT' => 'SELECT array_agg(DISTINCT c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and concatenation' => 'SELECT array_agg(DISTINCT c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY' => 'SELECT array_agg(c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY DESC' => 'SELECT array_agg(c0_.text1 ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and ORDER BY' => 'SELECT array_agg(DISTINCT c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with concatenation and ORDER BY' => 'SELECT array_agg(c0_.text1 || c0_.text2 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT, concatenation and ORDER BY' => 'SELECT array_agg(DISTINCT c0_.text1 || c0_.text2 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with multiple ORDER BY columns' => 'SELECT array_agg(c0_.text1 ORDER BY c0_.text1 ASC, c0_.text2 DESC) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf('SELECT ARRAY_AGG(e.text1) FROM %s e', ContainsTexts::class),
            'with concatenation' => \sprintf('SELECT ARRAY_AGG(CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with DISTINCT' => \sprintf('SELECT ARRAY_AGG(DISTINCT e.text1) FROM %s e', ContainsTexts::class),
            'with DISTINCT and concatenation' => \sprintf('SELECT ARRAY_AGG(DISTINCT CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with ORDER BY' => \sprintf('SELECT ARRAY_AGG(e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with ORDER BY DESC' => \sprintf('SELECT ARRAY_AGG(e.text1 ORDER BY e.text1 DESC) FROM %s e', ContainsTexts::class),
            'with DISTINCT and ORDER BY' => \sprintf('SELECT ARRAY_AGG(DISTINCT e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with concatenation and ORDER BY' => \sprintf('SELECT ARRAY_AGG(CONCAT(e.text1, e.text2) ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with DISTINCT, concatenation and ORDER BY' => \sprintf('SELECT ARRAY_AGG(DISTINCT CONCAT(e.text1, e.text2) ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with multiple ORDER BY columns' => \sprintf('SELECT ARRAY_AGG(e.text1 ORDER BY e.text1 ASC, e.text2 DESC) FROM %s e', ContainsTexts::class),
        ];
    }
}
