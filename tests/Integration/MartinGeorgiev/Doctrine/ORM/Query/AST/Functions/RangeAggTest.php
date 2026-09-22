<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg;
use PHPUnit\Framework\Attributes\Test;

final class RangeAggTest extends RangeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANGE_AGG' => RangeAgg::class,
        ];
    }

    #[Test]
    public function returns_the_aggregated_ranges_from_an_entity_field(): void
    {
        $dql = 'SELECT RANGE_AGG(t.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{[1,15)}', $result[0]['result']);
    }
}
