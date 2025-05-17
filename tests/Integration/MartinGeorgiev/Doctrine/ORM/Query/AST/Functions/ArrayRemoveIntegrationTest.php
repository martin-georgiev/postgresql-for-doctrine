<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;

class ArrayRemoveIntegrationTest extends IntegrationTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_REMOVE' => ArrayRemove::class];
    }

    public function test_array_remove_with_text_array(): void
    {
        $dql = "SELECT ARRAY_REMOVE(t.textArray, 'apple') as removed 
                FROM MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\Entity\\ArrayTest t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(['banana'], $result[0]['removed']);
    }

    public function test_array_remove_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.intArray, 1) as removed 
                FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals([2], $result[0]['removed']);
    }

    public function test_array_remove_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REMOVE(t.boolArray, true) as removed 
                FROM MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Entity\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals([false], $result[0]['removed']);
    }
}
