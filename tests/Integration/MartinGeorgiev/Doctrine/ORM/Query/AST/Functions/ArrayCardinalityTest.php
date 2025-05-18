<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;

class ArrayCardinalityTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_CARDINALITY' => ArrayCardinality::class];
    }

    public function test_array_cardinality_with_text_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                ORDER BY t.id';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['result']); // First row should have 3 elements
        $this->assertEquals(2, $result[1]['result']); // Second row should have 2 elements
        $this->assertEquals(4, $result[2]['result']); // Third row should have 4 elements
    }

    public function test_array_cardinality_with_integer_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                ORDER BY t.id';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['result']); // First row should have 3 elements
        $this->assertEquals(2, $result[1]['result']); // Second row should have 2 elements
        $this->assertEquals(4, $result[2]['result']); // Third row should have 4 elements
    }

    public function test_array_cardinality_with_boolean_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                ORDER BY t.id';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['result']); // First row should have 3 elements
        $this->assertEquals(2, $result[1]['result']); // Second row should have 2 elements
        $this->assertEquals(4, $result[2]['result']); // Third row should have 4 elements
    }
}
