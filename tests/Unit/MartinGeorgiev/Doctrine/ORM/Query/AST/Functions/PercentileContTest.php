<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsOrderedItems;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileCont;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PercentileContTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PERCENTILE_CONT' => PercentileCont::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'orders ascending by default' => 'SELECT percentile_cont(0.5) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'orders descending' => 'SELECT percentile_cont(0.9) WITHIN GROUP (ORDER BY c0_.decimal1 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'takes the fraction as a parameter' => 'SELECT percentile_cont(?) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'matches WITHIN case-insensitively' => 'SELECT percentile_cont(0.5) WITHIN GROUP (ORDER BY c0_.decimal1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            "keeps a fetch-joined collection's order out of the aggregate" => 'SELECT c0_.id AS id_0, o1_.id AS id_1, o1_.name AS name_2, o1_.position AS position_3, o1_.owner_id AS owner_id_4 FROM ContainsOrderedItems c0_ INNER JOIN OrderedItem o1_ ON c0_.id = o1_.owner_id WHERE c0_.id IN (SELECT c2_.id FROM ContainsOrderedItems c2_ INNER JOIN OrderedItem o3_ ON c2_.id = o3_.owner_id GROUP BY c2_.id HAVING percentile_cont(0.5) WITHIN GROUP (ORDER BY o3_.position ASC) > 1) ORDER BY o1_.position ASC',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'orders ascending by default' => \sprintf('SELECT PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.decimal1) FROM %s e', ContainsNumerics::class),
            'orders descending' => \sprintf('SELECT PERCENTILE_CONT(0.9 WITHIN GROUP ORDER BY e.decimal1 DESC) FROM %s e', ContainsNumerics::class),
            'takes the fraction as a parameter' => \sprintf('SELECT PERCENTILE_CONT(:fraction WITHIN GROUP ORDER BY e.decimal1) FROM %s e', ContainsNumerics::class),
            'matches WITHIN case-insensitively' => \sprintf('SELECT PERCENTILE_CONT(0.5 within group order by e.decimal1) FROM %s e', ContainsNumerics::class),
            "keeps a fetch-joined collection's order out of the aggregate" => \sprintf('SELECT o, i FROM %s o JOIN o.items i WHERE o.id IN (SELECT o2.id FROM %s o2 JOIN o2.items i2 GROUP BY o2.id HAVING PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY i2.position) > 1)', ContainsOrderedItems::class, ContainsOrderedItems::class),
        ];
    }

    #[DataProvider('provideMalformedInputs')]
    #[Test]
    public function throws_exception_for_malformed_input(string $dqlFunctionCall): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT %s FROM %s e', $dqlFunctionCall, ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedInputs(): array
    {
        return [
            'missing WITHIN GROUP' => ['PERCENTILE_CONT(0.5 ORDER BY e.decimal1)'],
            'WITHIN without GROUP' => ['PERCENTILE_CONT(0.5 WITHIN ORDER BY e.decimal1)'],
            'GROUP without ORDER BY' => ['PERCENTILE_CONT(0.5 WITHIN GROUP e.decimal1)'],
            'missing fraction' => ['PERCENTILE_CONT(WITHIN GROUP ORDER BY e.decimal1)'],
            'fraction separated by a comma' => ['PERCENTILE_CONT(0.5, WITHIN GROUP ORDER BY e.decimal1)'],
            'another word in place of WITHIN' => ['PERCENTILE_CONT(0.5 INSIDE GROUP ORDER BY e.decimal1)'],
            'several ORDER BY items' => ['PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.decimal1 DESC, e.decimal2 ASC)'],
        ];
    }
}
