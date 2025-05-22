<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists;

class JsonbPathExistsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSONB_PATH_EXISTS' => JsonbPathExists::class];
    }

    public function test_jsonb_path_exists_with_simple_path(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_path_exists_with_nested_path(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": {"b": 2}}',
            'path' => '$.a.b',
        ]);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_path_exists_with_missing_path(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1}',
            'path' => '$.b',
        ]);
        $this->assertFalse($result[0]['result']);
    }

    public function test_jsonb_path_exists_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.nested']);
        $this->assertTrue($result[0]['result']);
    }
}
