<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet;

class JsonbSetTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['JSONB_SET' => JsonbSet::class];
    }

    public function test_jsonb_set_with_text_value(): void
    {
        $dql = "SELECT JSONB_SET(JSONB_BUILD_OBJECT('name', t.textArray[1]), '{name}', t.textArray[2]) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals(['name' => 'banana'], \json_decode((string) $result[0]['result'], true));
    }

    public function test_jsonb_set_with_nested_path(): void
    {
        $dql = "SELECT JSONB_SET(JSONB_BUILD_OBJECT('user', JSONB_BUILD_OBJECT('name', t.textArray[1])), '{user,name}', t.textArray[2]) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals(['user' => ['name' => 'banana']], \json_decode((string) $result[0]['result'], true));
    }

    public function test_jsonb_set_with_array_value(): void
    {
        $dql = "SELECT JSONB_SET(JSONB_BUILD_OBJECT('fruits', '[]'), '{fruits}', t.textArray) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals(['fruits' => ['apple', 'banana', 'orange']], \json_decode((string) $result[0]['result'], true));
    }

    public function test_jsonb_set_with_create_missing(): void
    {
        $dql = "SELECT JSONB_SET(JSONB_BUILD_OBJECT('name', t.textArray[1]), '{age}', t.integerArray[1], true) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals(['name' => 'apple', 'age' => 1], \json_decode((string) $result[0]['result'], true));
    }
}
