<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg;

class JsonAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_AGG' => JsonAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic usage' => 'SELECT json_agg(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with concatenation' => 'SELECT json_agg(c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT' => 'SELECT json_agg(DISTINCT c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and concatenation' => 'SELECT json_agg(DISTINCT c0_.text1 || c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY' => 'SELECT json_agg(c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with ORDER BY DESC' => 'SELECT json_agg(c0_.text1 ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and ORDER BY' => 'SELECT json_agg(DISTINCT c0_.text1 ORDER BY c0_.text1 ASC) AS sclr_0 FROM ContainsTexts c0_',
            'with DISTINCT and ORDER BY DESC' => 'SELECT json_agg(DISTINCT c0_.text1 ORDER BY c0_.text1 DESC) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf('SELECT JSON_AGG(e.text1) FROM %s e', ContainsTexts::class),
            'with concatenation' => \sprintf('SELECT JSON_AGG(CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with DISTINCT' => \sprintf('SELECT JSON_AGG(DISTINCT e.text1) FROM %s e', ContainsTexts::class),
            'with DISTINCT and concatenation' => \sprintf('SELECT JSON_AGG(DISTINCT CONCAT(e.text1, e.text2)) FROM %s e', ContainsTexts::class),
            'with ORDER BY' => \sprintf('SELECT JSON_AGG(e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with ORDER BY DESC' => \sprintf('SELECT JSON_AGG(e.text1 ORDER BY e.text1 DESC) FROM %s e', ContainsTexts::class),
            'with DISTINCT and ORDER BY' => \sprintf('SELECT JSON_AGG(DISTINCT e.text1 ORDER BY e.text1) FROM %s e', ContainsTexts::class),
            'with DISTINCT and ORDER BY DESC' => \sprintf('SELECT JSON_AGG(DISTINCT e.text1 ORDER BY e.text1 DESC) FROM %s e', ContainsTexts::class),
        ];
    }
}
