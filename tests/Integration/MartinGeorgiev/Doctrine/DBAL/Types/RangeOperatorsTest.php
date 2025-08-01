<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;

class RangeOperatorsTest extends RangeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS' => Contains::class,
            'IS_CONTAINED_BY' => IsContainedBy::class,
            'OVERLAPS' => Overlaps::class,
        ];
    }

    public function test_range_contains_operator_int4range(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE CONTAINS(r.int4Range, \'[3,7)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_contains_operator_numrange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE CONTAINS(r.numRange, \'[2.5,8.5)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_contains_operator_int8range(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE CONTAINS(r.int8Range, \'[150,800)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_contains_operator_daterange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE CONTAINS(r.dateRange, \'[2023-02-01,2023-11-01)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_is_contained_by_int4range(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE IS_CONTAINED_BY(\'[3,7)\', r.int4Range) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_is_contained_by_int8range(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE IS_CONTAINED_BY(\'[200,800)\', r.int8Range) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_is_contained_by_daterange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE IS_CONTAINED_BY(\'[2023-02-01,2023-11-01)\', r.dateRange) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_range_overlaps_operator_int4range(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE OVERLAPS(r.int4Range, \'[8,12)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(2, $result); // Should match records 1 and 2
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_range_overlaps_operator_int8range(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE OVERLAPS(r.int8Range, \'[800,1200)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(2, $result); // Should match records 1 and 2
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_range_overlaps_operator_daterange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE OVERLAPS(r.dateRange, \'[2023-11-15,2024-01-15)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(3, $result); // Should match all 3 records as they all overlap with the test range
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
        $this->assertContains(['id' => 3], $result);
    }

    public function test_datetime_is_contained_by_tsrange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE IS_CONTAINED_BY(\'[2023-01-01 12:00:00,2023-01-01 16:00:00)\', r.tsRange) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_datetime_is_contained_by_tstzrange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE IS_CONTAINED_BY(\'[2023-01-01 12:00:00+00,2023-01-01 16:00:00+00)\', r.tstzRange) = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_datetime_overlaps_tsrange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE OVERLAPS(r.tsRange, \'[2023-01-01 16:00:00,2023-01-01 20:00:00)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result); // Should match record 1 where tsrange overlaps with the test range
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_datetime_overlaps_tstzrange(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE OVERLAPS(r.tstzRange, \'[2023-01-01 16:00:00+00,2023-01-01 20:00:00+00)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result); // Should match record 1 where tstzrange overlaps with the test range
        $this->assertSame(1, $result[0]['id']);
    }

    public function test_complex_range_query(): void
    {
        $dql = 'SELECT r.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges r 
                WHERE CONTAINS(r.int4Range, \'[3,7)\') = TRUE 
                AND CONTAINS(r.numRange, \'[2.5,8.5)\') = TRUE';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['id']);
    }
}
