<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;
use Tests\Integration\MartinGeorgiev\TestCase;

class ArrayRemoveIntegrationTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_REMOVE' => ArrayRemove::class];
    }

    public function test_array_remove_with_text_array(): void
    {
        $dql = "SELECT ARRAY_REMOVE(t.textArray, 'apple') as removed 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['removed']);
        $this->assertEquals(['banana', 'orange'], $actual);
    }

    public function test_array_remove_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.integerArray, 1) as removed 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['removed']);
        $this->assertEquals([2, 3], $actual);
    }

    public function test_array_remove_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.boolArray, true) as removed 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['removed']);
        $this->assertEquals([false], $actual);
    }
}
