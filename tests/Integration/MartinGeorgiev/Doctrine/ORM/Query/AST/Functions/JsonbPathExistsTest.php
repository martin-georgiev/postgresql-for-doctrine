<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists;
use PHPUnit\Framework\Attributes\Test;

class JsonbPathExistsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_EXISTS' => JsonbPathExists::class,
        ];
    }

    #[Test]
    public function can_check_simple_path_exists(): void
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

    #[Test]
    public function can_check_nested_path_exists(): void
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

    #[Test]
    public function returns_false_for_missing_path(): void
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

    #[Test]
    public function can_check_path_exists_in_column_reference(): void
    {
        $dql = 'SELECT JSONB_PATH_EXISTS(t.object1, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.name']);
        $this->assertTrue($result[0]['result']);
    }
}
