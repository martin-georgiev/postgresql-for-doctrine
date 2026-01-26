<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildArray;
use PHPUnit\Framework\Attributes\Test;

class JsonBuildArrayTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_BUILD_ARRAY' => JsonBuildArray::class,
        ];
    }

    #[Test]
    public function can_build_json_array(): void
    {
        $dql = "SELECT JSON_BUILD_ARRAY('a', 'b', 'c') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(['a', 'b', 'c'], $decoded);
    }

    #[Test]
    public function can_build_json_array_with_field_values(): void
    {
        $dql = "SELECT JSON_BUILD_ARRAY(JSON_GET_FIELD_AS_TEXT(t.jsonbObject1, 'name'), JSON_GET_FIELD_AS_TEXT(t.jsonbObject1, 'age')) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(['John', '30'], $decoded);
    }
}
