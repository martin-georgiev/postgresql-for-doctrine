<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions;

class ArrayPositionsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_POSITIONS' => ArrayPositions::class];
    }

    public function test_array_positions_with_text_array(): void
    {
        $dql = "SELECT ARRAY_POSITIONS(t.textArray, 'apple') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 2";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([2], $actual);
    }

    public function test_array_positions_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_POSITIONS(t.integerArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([2], $actual);
    }

    public function test_array_positions_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_POSITIONS(t.boolArray, true) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([2], $actual);
    }

    public function test_array_positions_with_not_found(): void
    {
        $dql = "SELECT ARRAY_POSITIONS(t.textArray, 'mango') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 2";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([], $actual);
    }
}
