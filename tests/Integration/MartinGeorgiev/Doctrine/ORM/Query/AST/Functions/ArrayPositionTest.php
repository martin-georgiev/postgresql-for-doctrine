<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition;
use PHPUnit\Framework\Attributes\Test;

class ArrayPositionTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_POSITION' => ArrayPosition::class,
        ];
    }

    #[Test]
    public function can_find_position_in_text_array(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.textArray, \'orange\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function can_find_position_in_integer_array(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.integerArray, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function can_find_position_in_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.boolArray, false) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_null_when_no_position_is_found(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.textArray, \'mango\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
