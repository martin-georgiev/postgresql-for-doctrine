<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg;

class JsonAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSON_AGG' => JsonAgg::class];
    }

    public function test_json_agg_with_text_array(): void
    {
        $dql = 'SELECT JSON_AGG(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(['apple', 'banana', 'orange'], \json_decode((string) $result[0]['result'], true));
    }

    public function test_json_agg_with_integer_array(): void
    {
        $dql = 'SELECT JSON_AGG(t.integerArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals([1, 2, 3], \json_decode((string) $result[0]['result'], true));
    }

    public function test_json_agg_with_boolean_array(): void
    {
        $dql = 'SELECT JSON_AGG(t.boolArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals([true, false, true], \json_decode((string) $result[0]['result'], true));
    }
}
