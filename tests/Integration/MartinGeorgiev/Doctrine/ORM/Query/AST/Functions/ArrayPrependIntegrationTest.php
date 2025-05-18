<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;

class ArrayPrependIntegrationTest extends IntegrationTestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_PREPEND' => ArrayPrepend::class];
    }

    public function test_array_prepend_with_text_array(): void
    {
        $dql = "SELECT ARRAY_PREPEND('orange', t.textArray) as prepended 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ArrayTest t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(['orange', 'apple', 'banana'], $result[0]['prepended']);
    }

    public function test_array_prepend_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(3, t.intArray) as prepended 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals([3, 1, 2], $result[0]['prepended']);
    }

    public function test_array_prepend_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_PREPEND(true, t.boolArray) as prepended 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ArrayTest t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals([true, true, false], $result[0]['prepended']);
    }
}
