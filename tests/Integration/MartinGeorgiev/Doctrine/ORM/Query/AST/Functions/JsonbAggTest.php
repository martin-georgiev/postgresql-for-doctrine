<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg;
use PHPUnit\Framework\Attributes\Test;

class JsonbAggTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_AGG' => JsonbAgg::class,
        ];
    }

    #[Test]
    public function can_aggregate_single_row_to_jsonb(): void
    {
        $dql = 'SELECT JSONB_AGG(t.jsonObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $actual = \json_decode($result[0]['result'], true);
        $this->assertIsArray($actual);
        $expected = [[
            'name' => 'John',
            'age' => 30,
            'tags' => ['developer', 'manager'],
            'address' => ['city' => 'New York'],
        ]];
        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    #[Test]
    public function can_aggregate_multiple_rows_to_jsonb(): void
    {
        $dql = 'SELECT JSONB_AGG(t.jsonObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id IN (1, 2)';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $actual = \json_decode($result[0]['result'], true);
        $this->assertIsArray($actual);
        $expected = [
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
        ];
        $this->assertEqualsCanonicalizing($expected, $actual);
    }
}
