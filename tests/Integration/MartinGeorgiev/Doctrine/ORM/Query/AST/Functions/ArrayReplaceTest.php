<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;

class ArrayReplaceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_REPLACE' => ArrayReplace::class];
    }

    public function test_array_replace_with_text_array(): void
    {
        $dql = "SELECT ARRAY_REPLACE(t.textArray, 'apple', 'pear') as replaced 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        \assert(isset($result[0]['replaced']));
        $actual = $this->transformPostgresArray($result[0]['replaced']);
        $this->assertEquals(['pear', 'banana', 'orange'], $actual);
    }

    public function test_array_replace_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.integerArray, 1, 10) as replaced 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        \assert(isset($result[0]['replaced']));
        $actual = $this->transformPostgresArray($result[0]['replaced']);
        $this->assertEquals([10, 2, 3], $actual);
    }

    public function test_array_replace_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_REPLACE(t.boolArray, true, false) as replaced 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        \assert(isset($result[0]['replaced']));
        $actual = $this->transformPostgresArray($result[0]['replaced']);
        $this->assertEquals([false, false, false], $actual);
    }
}
