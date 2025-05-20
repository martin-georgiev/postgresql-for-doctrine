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

    public function test_jsonb_path_exists_simple(): void
    {
        $dql = "SELECT JSONB_PATH_EXISTS('{\"a\": 1, \"b\": 2}', '$.b') as result";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_path_exists_nested(): void
    {
        $dql = "SELECT JSONB_PATH_EXISTS('{\"a\": {\"b\": 2}}', '$.a.b') as result";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    public function test_jsonb_path_not_exists(): void
    {
        $dql = "SELECT JSONB_PATH_EXISTS('{\"a\": 1}', '$.b') as result";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    public function test_jsonb_path_exists_with_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.nested']);
        $this->assertIsBool($result[0]['result']);
    }
}
