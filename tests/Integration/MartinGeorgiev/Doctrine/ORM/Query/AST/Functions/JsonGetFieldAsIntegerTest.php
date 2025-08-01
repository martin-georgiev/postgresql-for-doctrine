<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger;

class JsonGetFieldAsIntegerTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
            'JSON_GET_FIELD_AS_INTEGER' => JsonGetFieldAsInteger::class,
        ];
    }

    public function test_json_get_field_as_integer(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(30, $result[0]['result']);
    }

    public function test_json_get_field_as_integer_with_index(): void
    {
        // First, let's insert test data with numeric arrays
        $this->connection->executeStatement(
            \sprintf("UPDATE %s.containsjsons SET object1 = '{\"scores\": [85, 92, 78]}' WHERE id = 1", self::DATABASE_SCHEMA)
        );

        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(JSON_GET_FIELD(t.object1, 'scores'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(92, $result[0]['result']);
    }

    public function test_json_get_field_as_integer_empty_object(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    public function test_json_get_field_as_integer_null_value(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 5";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    public function test_json_get_field_as_integer_nonexistent_property_name(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.object1, 'nonexistent') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
