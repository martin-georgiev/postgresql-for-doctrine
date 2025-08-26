<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray;
use PHPUnit\Framework\Attributes\Test;

class InArrayTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IN_ARRAY' => InArray::class,
        ];
    }

    #[Test]
    public function in_array_with_text_element(): void
    {
        $dql = 'SELECT IN_ARRAY(:value, t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => 'banana']);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function in_array_with_integer_element(): void
    {
        $dql = 'SELECT IN_ARRAY(:value, t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => 2]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function in_array_with_non_existing_element(): void
    {
        $dql = 'SELECT IN_ARRAY(:value, t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => 'mango']);
        $this->assertFalse($result[0]['result']);
    }
}
