<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsOrderedItems;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg;

final class StringAggTest extends TestCase
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
            "keeps a fetch-joined collection's order out of the aggregate" => 'SELECT c0_.id AS id_0, o1_.id AS id_1, o1_.name AS name_2, o1_.position AS position_3, o1_.owner_id AS owner_id_4 FROM ContainsOrderedItems c0_ INNER JOIN OrderedItem o1_ ON c0_.id = o1_.owner_id WHERE c0_.id IN (SELECT c2_.id FROM ContainsOrderedItems c2_ INNER JOIN OrderedItem o3_ ON c2_.id = o3_.owner_id GROUP BY c2_.id HAVING string_agg(o3_.name, \',\' ORDER BY o3_.name ASC) <> \'\') ORDER BY o1_.position ASC',
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
            "keeps a fetch-joined collection's order out of the aggregate" => \sprintf('SELECT o, i FROM %s o JOIN o.items i WHERE o.id IN (SELECT o2.id FROM %s o2 JOIN o2.items i2 GROUP BY o2.id HAVING STRING_AGG(i2.name, \',\' ORDER BY i2.name) <> \'\')', ContainsOrderedItems::class, ContainsOrderedItems::class),
        ];
    }
}
