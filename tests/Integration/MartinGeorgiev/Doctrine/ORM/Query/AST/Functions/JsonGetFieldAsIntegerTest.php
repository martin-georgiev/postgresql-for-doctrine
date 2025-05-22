<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger;

class JsonGetFieldAsIntegerTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD_AS_INTEGER' => JsonGetFieldAsInteger::class,
        ];
    }

    public function test_json_get_field_as_integer(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(30, $result[0]['result']);
    }

    public function test_json_get_field_as_integer_empty_object(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNull($result[0]['result']);
    }

    public function test_json_get_field_as_integer_null_value(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 5";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNull($result[0]['result']);
    }

    public function test_json_get_field_as_integer_nonexistent_field(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'nonexistent') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertNull($result[0]['result']);
    }
}
