<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeIntersectAgg;
use PHPUnit\Framework\Attributes\Test;

final class RangeIntersectAggTest extends RangeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANGE_INTERSECT_AGG' => RangeIntersectAgg::class,
        ];
    }

    #[Test]
    public function returns_the_intersected_ranges_from_an_entity_field(): void
    {
        $dql = 'SELECT RANGE_INTERSECT_AGG(t.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[5,10)', $result[0]['result']);
    }
}
