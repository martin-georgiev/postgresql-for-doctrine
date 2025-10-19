<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg;

class JsonbAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_AGG' => JsonbAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic usage' => 'SELECT jsonb_agg(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with concatenation' => 'SELECT jsonb_agg(c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT' => 'SELECT jsonb_agg(DISTINCT c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and concatenation' => 'SELECT jsonb_agg(DISTINCT c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY' => 'SELECT jsonb_agg(c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY DESC' => 'SELECT jsonb_agg(c0_.text1 ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and ORDER BY' => 'SELECT jsonb_agg(DISTINCT c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and ORDER BY DESC' => 'SELECT jsonb_agg(DISTINCT c0_.text1 ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf('SELECT JSONB_AGG(e.text1) FROM %s e', ContainsTexts::class),
            'with concatenation' => \sprintf('SELECT JSONB_AGG(CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with DISTINCT' => \sprintf('SELECT JSONB_AGG(DISTINCT e.text1) FROM %s e', ContainsTexts::class),
            'with DISTINCT and concatenation' => \sprintf('SELECT JSONB_AGG(DISTINCT CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with ORDER BY' => \sprintf('SELECT JSONB_AGG(e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with ORDER BY DESC' => \sprintf('SELECT JSONB_AGG(e.text1 ORDER BY e.text1 DESC) FROM %s e', ContainsTexts::class),
            'with DISTINCT and ORDER BY' => \sprintf('SELECT JSONB_AGG(DISTINCT e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with DISTINCT and ORDER BY DESC' => \sprintf('SELECT JSONB_AGG(DISTINCT e.text1 ORDER BY e.text1 DESC) FROM %s e', ContainsTexts::class),
        ];
    }
}
