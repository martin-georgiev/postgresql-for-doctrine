<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;

class ArrayDimensionsTest extends ArrayTestCase
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
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[1:3]', $result[0]['result']);
    }

    public function test_array_dimensions_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[1:3]', $result[0]['result']);
    }

    public function test_array_dimensions_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[1:3]', $result[0]['result']);
    }
}
