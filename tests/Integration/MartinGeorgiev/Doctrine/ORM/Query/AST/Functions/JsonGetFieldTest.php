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
    public function json_get_field_with_property_name(): void
    {
        $dql = "SELECT JSON_GET_FIELD(t.object1, 'name') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"John"', $result[0]['result']);
    }

    #[Test]
    public function json_get_field_with_index(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.object1, 'tags'), 0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"developer"', $result[0]['result']);
    }

    #[Test]
    public function json_get_field_nested_object_access(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.object1, 'address'), 'city') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"New York"', $result[0]['result']);
    }

    #[Test]
    public function json_get_field_with_empty_array(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.object1, 'tags'), 0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function json_get_field_with_nonexistent_index(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.object1, 'tags'), 10) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function json_get_field_with_nonexistent_property_name(): void
    {
        $dql = "SELECT JSON_GET_FIELD(t.object1, 'nonexistent') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
