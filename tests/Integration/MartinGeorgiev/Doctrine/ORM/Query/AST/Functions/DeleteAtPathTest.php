<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath;

class DeleteAtPathTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return ['DELETE_AT_PATH' => DeleteAtPath::class];
    }

    public function test_delete_at_path_simple(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(\'{"a": {"b": {"c": "value"}}}\', \'{a,b}\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{"a": {}}', $result[0]['result']);
    }

    public function test_delete_at_path_multiple_elements(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(\'{"a": {"b": 1, "c": 2}}\', \'{a,b}\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{"a": {"c": 2}}', $result[0]['result']);
    }

    public function test_delete_at_path_with_array(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(\'{"a": [1, 2, 3]}\', \'{a,1}\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{"a": [1, 3]}', $result[0]['result']);
    }

    public function test_delete_at_path_with_column_reference(): void
    {
        $dql = 'SELECT DELETE_AT_PATH(t.object1, \'{nested,value}\') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertJson($result[0]['result']);
    }
}
