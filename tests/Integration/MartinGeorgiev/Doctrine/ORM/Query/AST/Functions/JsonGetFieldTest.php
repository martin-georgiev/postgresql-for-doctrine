<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

class JsonGetFieldTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function returns_json_string_when_getting_field_by_property_name(): void
    {
        $dql = "SELECT JSON_GET_FIELD(t.jsonObject1, 'name') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"John"', $result[0]['result']);
    }

    #[Test]
    public function returns_json_string_when_getting_field_by_index(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.jsonObject1, 'tags'), 0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"developer"', $result[0]['result']);
    }

    #[Test]
    public function returns_json_string_when_accessing_nested_object(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.jsonObject1, 'address'), 'city') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"New York"', $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_empty_array(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.jsonObject1, 'tags'), 0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_null_for_nonexistent_index(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.jsonObject1, 'tags'), 10) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_null_for_nonexistent_property(): void
    {
        $dql = "SELECT JSON_GET_FIELD(t.jsonObject1, 'nonexistent') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
