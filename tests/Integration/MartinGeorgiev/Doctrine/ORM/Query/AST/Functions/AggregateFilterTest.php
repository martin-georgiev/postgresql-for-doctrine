<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFilter;
use PHPUnit\Framework\Attributes\Test;

final class AggregateFilterTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FILTER' => AggregateFilter::class,
        ];
    }

    #[Test]
    public function returns_the_filtered_aggregate_from_a_literal(): void
    {
        $dql = 'SELECT FILTER(SUM(n.integer1), WHERE n.integer2 = 20) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_filtered_aggregate_from_an_entity_field(): void
    {
        $dql = 'SELECT FILTER(SUM(n.integer1), WHERE n.integer1 < n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
