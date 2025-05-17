<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend;

class ArrayAppendIntegrationTest extends IntegrationTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_APPEND' => ArrayAppend::class];
    }

    public function test_array_append_with_text_array(): void
    {
        $dql = "SELECT ARRAY_APPEND(t.textArray, 'orange') as appended 
                FROM MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\Entity\\ArrayTest t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(['apple', 'banana', 'orange'], $result[0]['appended']);
    }

    public function test_array_append_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.intArray, 3) as appended 
                FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals([1, 2, 3], $result[0]['appended']);
    }

    public function test_array_append_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.boolArray, true) as appended 
                FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals([true, false, true], $result[0]['appended']);
    }
}
