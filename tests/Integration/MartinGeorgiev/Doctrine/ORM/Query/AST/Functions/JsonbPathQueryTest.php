<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQuery;

class JsonbPathQueryTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY' => JsonbPathQuery::class,
        ];
    }

    public function test_jsonb_path_query_simple(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame('2', $result[0]['result']);
    }

    public function test_jsonb_path_query_array(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [1, 2, 3]}',
            'path' => '$.items[*]',
        ]);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertSame('1', $result[0]['result']);
        $this->assertSame('2', $result[1]['result']);
        $this->assertSame('3', $result[2]['result']);
    }

    public function test_jsonb_path_query_with_filter(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [{"id": 1}, {"id": 2}, {"id": 3}]}',
            'path' => '$.items[*] ? (@.id > 1)',
        ]);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('{"id": 2}', $result[0]['result']);
        $this->assertSame('{"id": 3}', $result[1]['result']);
    }

    public function test_jsonb_path_query_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.tags[*]']);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('"developer"', $result[0]['result']);
        $this->assertSame('"manager"', $result[1]['result']);
    }
}
