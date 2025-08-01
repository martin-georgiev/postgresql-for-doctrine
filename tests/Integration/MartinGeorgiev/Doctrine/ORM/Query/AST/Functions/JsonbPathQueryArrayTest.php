<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray;

class JsonbPathQueryArrayTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY_ARRAY' => JsonbPathQueryArray::class,
        ];
    }

    public function test_jsonb_path_query_array_simple(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_ARRAY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(1, $decoded);
        $this->assertSame(2, $decoded[0]);
    }

    public function test_jsonb_path_query_array_multiple_values(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_ARRAY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [1, 2, 3]}',
            'path' => '$.items[*]',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(3, $decoded);
        $this->assertSame([1, 2, 3], $decoded);
    }

    public function test_jsonb_path_query_array_with_filter(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_ARRAY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [{"id": 1}, {"id": 2}, {"id": 3}]}',
            'path' => '$.items[*] ? (@.id > 1)',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(2, $decoded);
        $this->assertSame(['id' => 2], $decoded[0]);
        $this->assertSame(['id' => 3], $decoded[1]);
    }

    public function test_jsonb_path_query_array_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_ARRAY(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.tags[*]']);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(2, $decoded);
        $this->assertSame(['developer', 'manager'], $decoded);
    }
}
