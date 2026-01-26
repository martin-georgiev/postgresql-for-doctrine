<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildArray;
use PHPUnit\Framework\Attributes\Test;

class JsonbBuildArrayTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_BUILD_ARRAY' => JsonbBuildArray::class,
        ];
    }

    #[Test]
    public function can_build_jsonb_array(): void
    {
        $dql = "SELECT JSONB_BUILD_ARRAY('a', 'b', 'c') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(['a', 'b', 'c'], $decoded);
    }

    #[Test]
    public function can_build_jsonb_array_with_field_values(): void
    {
        $dql = "SELECT JSONB_BUILD_ARRAY(JSON_GET_FIELD_AS_TEXT(t.jsonbObject1, 'name'), JSON_GET_FIELD_AS_TEXT(t.jsonbObject1, 'age')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(['John', '30'], $decoded);
    }
}
