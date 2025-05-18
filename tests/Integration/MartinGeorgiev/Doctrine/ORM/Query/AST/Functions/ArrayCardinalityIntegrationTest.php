<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;

class ArrayCardinalityIntegrationTest extends IntegrationTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_CARDINALITY' => ArrayCardinality::class];
    }

    public function test_array_cardinality_with_text_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.textArray) as cardinality 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ArrayTest t 
                ORDER BY t.id';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['cardinality']); // First row should have 3 elements
        $this->assertEquals(2, $result[1]['cardinality']); // Second row should have 2 elements
        $this->assertEquals(4, $result[2]['cardinality']); // Third row should have 4 elements
    }

    public function test_array_cardinality_with_integer_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.intArray) as cardinality 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ArrayTest t 
                ORDER BY t.id';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['cardinality']); // First row should have 3 elements
        $this->assertEquals(2, $result[1]['cardinality']); // Second row should have 2 elements
        $this->assertEquals(4, $result[2]['cardinality']); // Third row should have 4 elements
    }

    public function test_array_cardinality_with_boolean_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.boolArray) as cardinality 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ArrayTest t 
                ORDER BY t.id';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['cardinality']); // First row should have 3 elements
        $this->assertEquals(2, $result[1]['cardinality']); // Second row should have 2 elements
        $this->assertEquals(4, $result[2]['cardinality']); // Third row should have 4 elements
    }
}
