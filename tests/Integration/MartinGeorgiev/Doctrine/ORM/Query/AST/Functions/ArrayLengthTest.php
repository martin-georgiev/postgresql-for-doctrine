<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength;
use PHPUnit\Framework\Attributes\Test;

class ArrayLengthTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_LENGTH' => ArrayLength::class,
        ];
    }

    #[Test]
    public function can_get_text_array_length(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.textArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function can_get_integer_array_length(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.integerArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function can_get_boolean_array_length(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.boolArray, 1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_invalid_dimension(): void
    {
        $dql = 'SELECT ARRAY_LENGTH(t.textArray, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
