<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryFirst;

class JsonbPathQueryFirstTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY_FIRST' => JsonbPathQueryFirst::class,
        ];
    }

    public function test_jsonb_path_query_first_simple(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_FIRST(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertSame('2', $result[0]['result']);
    }

    public function test_jsonb_path_query_first_array(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_FIRST(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [1, 2, 3]}',
            'path' => '$.items[*]',
        ]);
        $this->assertSame('1', $result[0]['result']);
    }

    public function test_jsonb_path_query_first_with_filter(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_FIRST(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [{"id": 1}, {"id": 2}, {"id": 3}]}',
            'path' => '$.items[*] ? (@.id > 1)',
        ]);
        $this->assertSame('{"id": 2}', $result[0]['result']);
    }

    public function test_jsonb_path_query_first_with_no_match(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_FIRST(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"items": [{"id": 1}]}',
            'path' => '$.items[*] ? (@.id > 1)',
        ]);
        $this->assertNull($result[0]['result']);
    }

    public function test_jsonb_path_query_first_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_FIRST(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.tags[*]']);
        $this->assertSame('"developer"', $result[0]['result']);
    }
}
