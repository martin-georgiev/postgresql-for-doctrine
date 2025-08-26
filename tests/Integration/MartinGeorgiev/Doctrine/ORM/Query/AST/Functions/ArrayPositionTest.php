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
    public function array_position_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.textArray, \'banana\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function array_position_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.integerArray, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function array_position_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.boolArray, false) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function array_position_with_not_found_element(): void
    {
        $dql = 'SELECT ARRAY_POSITION(t.textArray, \'mango\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
