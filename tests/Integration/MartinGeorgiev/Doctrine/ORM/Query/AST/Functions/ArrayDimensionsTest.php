<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;

class ArrayDimensionsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_DIMENSIONS' => ArrayDimensions::class];
    }

    public function test_array_dimensions_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([3], $actual);
    }

    public function test_array_dimensions_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([3], $actual);
    }

    public function test_array_dimensions_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([3], $actual);
    }
}
