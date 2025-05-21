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
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_array_cardinality_with_integer_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_array_cardinality_with_boolean_array(): void
    {
        $dql = 'SELECT t.id, ARRAY_CARDINALITY(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }
}
