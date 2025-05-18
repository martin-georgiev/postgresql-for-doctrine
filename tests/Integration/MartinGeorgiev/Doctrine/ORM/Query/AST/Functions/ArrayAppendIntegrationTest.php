<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend;
use Tests\Integration\MartinGeorgiev\TestCase;

class ArrayAppendIntegrationTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_APPEND' => ArrayAppend::class];
    }

    public function test_array_append_with_text_array(): void
    {
        $dql = "SELECT ARRAY_APPEND(t.textArray, 'orange') as appended 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['appended']);
        $this->assertEquals(['apple', 'banana', 'orange', 'orange'], $actual);
    }

    public function test_array_append_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.integerArray, 3) as appended 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['appended']);
        $this->assertEquals([1, 2, 3, 3], $actual);
    }

    public function test_array_append_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_APPEND(t.boolArray, true) as appended 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['appended']);
        $this->assertEquals([true, false, true, true], $actual);
    }
}
