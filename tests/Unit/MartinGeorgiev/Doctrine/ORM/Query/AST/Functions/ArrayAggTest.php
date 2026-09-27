<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsOrderedItems;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;

final class ArrayAggTest extends TestCase
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
            "keeps a fetch-joined collection's order out of the aggregate" => 'SELECT c0_.id AS id_0, o1_.id AS id_1, o1_.name AS name_2, o1_.position AS position_3, o1_.owner_id AS owner_id_4 FROM ContainsOrderedItems c0_ INNER JOIN OrderedItem o1_ ON c0_.id = o1_.owner_id WHERE c0_.id IN (SELECT c2_.id FROM ContainsOrderedItems c2_ INNER JOIN OrderedItem o3_ ON c2_.id = o3_.owner_id GROUP BY c2_.id HAVING array_agg(o3_.name ORDER BY o3_.name ASC) IS NOT NULL) ORDER BY o1_.position ASC',
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
            "keeps a fetch-joined collection's order out of the aggregate" => \sprintf('SELECT o, i FROM %s o JOIN o.items i WHERE o.id IN (SELECT o2.id FROM %s o2 JOIN o2.items i2 GROUP BY o2.id HAVING ARRAY_AGG(i2.name ORDER BY i2.name) IS NOT NULL)', ContainsOrderedItems::class, ContainsOrderedItems::class),
        ];
    }
}
