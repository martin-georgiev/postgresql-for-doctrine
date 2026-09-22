<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

final class JsonGetFieldTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function returns_the_json_field_by_property_name_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_GET_FIELD(t.jsonObject1, 'name') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"John"', $result[0]['result']);
    }

    #[Test]
    public function returns_the_json_field_by_index_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_GET_FIELD(JSON_GET_FIELD(t.jsonObject1, 'tags'), 0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"developer"', $result[0]['result']);
    }
}
