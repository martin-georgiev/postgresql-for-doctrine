<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath;
use PHPUnit\Framework\Attributes\Test;

class DeleteAtPathTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DELETE_AT_PATH' => DeleteAtPath::class,
        ];
    }

    #[Test]
    public function can_delete_simple_path(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": {"b": {"c": "value"}}}',
            'path' => '{a,b}',
        ]);
        $this->assertSame('{"a": {}}', $result[0]['result']);
    }

    #[Test]
    public function can_delete_multiple_elements(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": {"b": 1, "c": 2}}',
            'path' => '{a,b}',
        ]);
        $this->assertSame('{"a": {"c": 2}}', $result[0]['result']);
    }

    #[Test]
    public function can_delete_array_element(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": [1, 2, 3]}',
            'path' => '{a,1}',
        ]);
        $this->assertSame('{"a": [1, 3]}', $result[0]['result']);
    }

    #[Test]
    public function can_delete_with_column_reference(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(t.jsonbObject1, :path) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '{nested,value}']);
        $this->assertIsString($result[0]['result']);
        $this->assertJson($result[0]['result']);
    }
}
