<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg;

class JsonbAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSONB_AGG' => JsonbAgg::class];
    }

    public function test_jsonb_agg_with_text_array(): void
    {
        $dql = 'SELECT JSONB_AGG(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals(['apple', 'banana', 'orange'], \json_decode($result[0]['result'], true));
    }

    public function test_jsonb_agg_with_integer_array(): void
    {
        $dql = 'SELECT JSONB_AGG(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals([1, 2, 3], \json_decode($result[0]['result'], true));
    }

    public function test_jsonb_agg_with_boolean_array(): void
    {
        $dql = 'SELECT JSONB_AGG(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals([true, false, true], \json_decode($result[0]['result'], true));
    }
}
