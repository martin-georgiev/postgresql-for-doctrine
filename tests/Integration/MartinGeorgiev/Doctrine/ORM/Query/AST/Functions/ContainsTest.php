<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;

class ContainsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['CONTAINS' => Contains::class];
    }

    public function test_contains_with_text_array(): void
    {
        $dql = 'SELECT CONTAINS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['banana']]);
        $this->assertTrue($result[0]['result']);
    }

    public function test_contains_with_integer_array(): void
    {
        $dql = 'SELECT CONTAINS(t.integerArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => [2]]);
        $this->assertTrue($result[0]['result']);
    }

    public function test_contains_with_non_existing_element(): void
    {
        $dql = 'SELECT CONTAINS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['value' => ['mango']]);
        $this->assertFalse($result[0]['result']);
    }
}
