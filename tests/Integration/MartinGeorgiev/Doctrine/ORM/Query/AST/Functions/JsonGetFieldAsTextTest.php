<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText;
use PHPUnit\Framework\Attributes\Test;

class JsonGetFieldAsTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
            'JSON_GET_FIELD_AS_TEXT' => JsonGetFieldAsText::class,
        ];
    }

    #[Test]
    public function json_get_field_as_text_with_property_name(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_TEXT(t.object1, 'name') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('John', $result[0]['result']);
    }

    #[Test]
    public function json_get_field_as_text_with_index(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_TEXT(JSON_GET_FIELD(t.object1, 'tags'), 0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('developer', $result[0]['result']);
    }

    #[Test]
    public function json_get_field_as_text_nested_access(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_TEXT(JSON_GET_FIELD(t.object1, 'address'), 'city') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('New York', $result[0]['result']);
    }

    #[Test]
    public function json_get_field_as_text_with_null_value(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_TEXT(t.object1, 'age') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 5";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function json_get_field_as_text_with_nonexistent_index(): void
    {
        $dql = "SELECT JSON_GET_FIELD_AS_TEXT(JSON_GET_FIELD(t.object1, 'tags'), 10) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
