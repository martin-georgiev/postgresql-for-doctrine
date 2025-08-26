<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions;
use PHPUnit\Framework\Attributes\Test;

class ArrayNumberOfDimensionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_NUMBER_OF_DIMENSIONS' => ArrayNumberOfDimensions::class,
        ];
    }

    #[Test]
    public function can_get_text_array_number_of_dimensions(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_get_integer_array_number_of_dimensions(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function can_get_boolean_array_number_of_dimensions(): void
    {
        $dql = 'SELECT ARRAY_NUMBER_OF_DIMENSIONS(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1, $result[0]['result']);
    }
}
