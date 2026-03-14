<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg;
use PHPUnit\Framework\Attributes\Test;

class RangeAggTest extends RangeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANGE_AGG' => RangeAgg::class,
        ];
    }

    #[Test]
    public function can_aggregate_overlapping_int4_ranges_into_multirange(): void
    {
        $dql = 'SELECT RANGE_AGG(t.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{[1,15)}', $result[0]['result']);
    }

    #[Test]
    public function can_aggregate_single_int4_range(): void
    {
        $dql = 'SELECT RANGE_AGG(t.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{[1,10)}', $result[0]['result']);
    }
}
