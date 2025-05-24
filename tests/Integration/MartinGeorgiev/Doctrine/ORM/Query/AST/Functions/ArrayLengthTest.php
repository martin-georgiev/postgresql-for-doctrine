<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength;

class ArrayLengthTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_LENGTH' => ArrayLength::class];
    }

    public function test_array_length_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.textArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_array_length_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.integerArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_array_length_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.boolArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    public function test_array_length_with_invalid_dimension(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.textArray, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
