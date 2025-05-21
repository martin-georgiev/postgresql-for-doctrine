<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;

class ArrayCardinalityTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_CARDINALITY' => ArrayCardinality::class];
    }

    public function test_array_cardinality_with_text_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(3, $result[0]['result']); // First row should have 3 elements
    }

    public function test_array_cardinality_with_integer_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(2, $result[1]['result']); // Second row should have 2 elements
    }

    public function test_array_cardinality_with_boolean_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(4, $result[2]['result']); // Third row should have 4 elements
    }
}
