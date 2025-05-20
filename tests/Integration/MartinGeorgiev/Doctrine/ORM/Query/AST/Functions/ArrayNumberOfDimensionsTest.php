<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions;

class ArrayNumberOfDimensionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_NUMBER_OF_DIMENSIONS' => ArrayNumberOfDimensions::class];
    }

    public function test_array_number_of_dimensions_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }

    public function test_array_number_of_dimensions_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }

    public function test_array_number_of_dimensions_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
