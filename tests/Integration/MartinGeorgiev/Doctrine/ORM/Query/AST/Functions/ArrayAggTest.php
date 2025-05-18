<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;

class ArrayAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARRAY_AGG' => ArrayAgg::class];
    }

    public function test_array_agg_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.textArray[1]) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals(['apple', 'grape', 'banana'], $actual);
    }

    public function test_array_agg_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.integerArray[1]) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([1, 4, 2], $actual);
    }

    public function test_array_agg_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.boolArray[1]) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([true, false, true], $actual);
    }
}
