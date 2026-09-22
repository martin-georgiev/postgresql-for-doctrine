<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger;
use PHPUnit\Framework\Attributes\Test;

final class JsonGetFieldAsIntegerTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
            'JSON_GET_FIELD_AS_INTEGER' => JsonGetFieldAsInteger::class,
        ];
    }

    #[Test]
    public function returns_the_integer_field_by_property_name_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(t.jsonObject1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(30, $result[0]['result']);
    }

    #[Test]
    public function returns_the_integer_field_by_index_from_an_entity_field(): void
    {
        // First, let's insert test data with numeric arrays
        $this->connection->executeStatement(
            \sprintf("UPDATE %s.containsjsons SET jsonObject1 = '{\"scores\": [85, 92, 78]}' WHERE id = 1", self::DATABASE_SCHEMA)
        );

        $dql = "SELECT JSON_GET_FIELD_AS_INTEGER(JSON_GET_FIELD(t.jsonObject1, 'scores'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(92, $result[0]['result']);
    }
}
