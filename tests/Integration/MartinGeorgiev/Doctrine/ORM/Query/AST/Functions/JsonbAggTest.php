<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg;

class JsonbAggTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSONB_AGG' => JsonbAgg::class];
    }

    public function test_jsonb_agg_with_single_row(): void
    {
        $dql = 'SELECT JSONB_AGG(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame([
            [
                'name' => 'John',
                'age' => 30,
                'tags' => ['developer', 'manager'],
                'address' => ['city' => 'New York'],
            ],
        ], $decoded);
    }

    public function test_jsonb_agg_with_all_rows(): void
    {
        $dql = 'SELECT JSONB_AGG(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame([
            [
                'name' => 'John',
                'age' => 30,
                'tags' => ['developer', 'manager'],
                'address' => ['city' => 'New York'],
            ],
            [
                'name' => 'Jane',
                'age' => 25,
                'tags' => ['designer'],
                'address' => ['city' => 'Boston'],
            ],
        ], $decoded);
    }

    public function test_jsonb_agg_with_object2_column(): void
    {
        $dql = 'SELECT JSONB_AGG(t.object2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame([
            [
                'name' => 'John',
                'age' => 30,
                'tags' => ['developer', 'manager'],
                'address' => ['city' => 'New York'],
            ],
            [
                'name' => 'Jane',
                'age' => 25,
                'tags' => ['designer'],
                'address' => ['city' => 'Boston'],
            ],
        ], $decoded);
    }
}
