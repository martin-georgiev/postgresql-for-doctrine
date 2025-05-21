<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest;

class ArrayAggTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
            'UNNEST' => Unnest::class,
        ];
    }

    public function test_array_agg_with_text_values(): void
    {
        $dql = 'SELECT ARRAY_AGG(UNNEST(t.textArray)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1
                GROUP BY t.id';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertEquals(['apple', 'banana', 'orange'], $decoded);
    }

    public function test_array_agg_with_integer_values(): void
    {
        $dql = 'SELECT ARRAY_AGG(UNNEST(t.integerArray)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1
                GROUP BY t.id';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertEquals([1, 2, 3], $decoded);
    }

    public function test_array_agg_with_distinct(): void
    {
        $dql = 'SELECT ARRAY_AGG(DISTINCT UNNEST(t.integerArray)) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t
                WHERE t.id = 1
                GROUP BY t.id';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertEquals([1, 2, 3], $decoded);
    }
}
