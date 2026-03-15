<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeIntersectAgg;
use PHPUnit\Framework\Attributes\Test;

class RangeIntersectAggTest extends RangeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANGE_INTERSECT_AGG' => RangeIntersectAgg::class,
        ];
    }

    #[Test]
    public function can_compute_intersection_of_overlapping_int4_ranges(): void
    {
        $dql = 'SELECT RANGE_INTERSECT_AGG(t.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[5,10)', $result[0]['result']);
    }

    #[Test]
    public function can_compute_intersection_of_single_int4_range(): void
    {
        $dql = 'SELECT RANGE_INTERSECT_AGG(t.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[1,10)', $result[0]['result']);
    }
}
