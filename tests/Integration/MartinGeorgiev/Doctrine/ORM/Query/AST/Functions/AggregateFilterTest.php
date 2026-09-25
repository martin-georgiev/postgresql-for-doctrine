<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFilter;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileCont;
use PHPUnit\Framework\Attributes\Test;

final class AggregateFilterTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
            'FILTER' => AggregateFilter::class,
            'PERCENTILE_CONT' => PercentileCont::class,
        ];
    }

    #[Test]
    public function returns_the_filtered_aggregate_from_a_literal(): void
    {
        $dql = 'SELECT FILTER(SUM(5), WHERE n.integer1 < n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_filtered_aggregate_from_an_entity_field(): void
    {
        $dql = 'SELECT FILTER(SUM(n.integer1), WHERE n.integer1 < n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_filtered_aggregate_with_a_library_aggregate(): void
    {
        $dql = 'SELECT FILTER(ARRAY_AGG(n.integer1), WHERE n.integer1 < n.integer2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{10}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_filtered_aggregate_with_an_ordered_set_aggregate(): void
    {
        $dql = 'SELECT FILTER(PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY n.decimal1), WHERE n.integer1 < n.integer2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10.5, $result[0]['result']);
    }

    #[Test]
    public function rejects_a_scalar_function(): void
    {
        $this->expectException(ParserException::class);
        $dql = 'SELECT FILTER(ABS(n.integer1), WHERE n.integer1 < n.integer2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function rejects_a_nested_filter(): void
    {
        $this->expectException(ParserException::class);
        $dql = 'SELECT FILTER(FILTER(SUM(n.integer1), WHERE n.integer1 > 5), WHERE n.integer1 < n.integer2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n WHERE n.id = 1';
        $this->executeDqlQuery($dql);
    }
}
