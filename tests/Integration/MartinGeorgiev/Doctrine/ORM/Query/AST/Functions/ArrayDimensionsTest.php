<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;
use PHPUnit\Framework\Attributes\Test;

class ArrayDimensionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_DIMENSIONS' => ArrayDimensions::class,
        ];
    }

    #[Test]
    public function can_get_text_array_dimensions(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1:3]', $result[0]['result']);
    }

    #[Test]
    public function can_get_integer_array_dimensions(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1:3]', $result[0]['result']);
    }

    #[Test]
    public function can_get_boolean_array_dimensions(): void
    {
        $dql = 'SELECT ARRAY_DIMENSIONS(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[1:3]', $result[0]['result']);
    }
}
