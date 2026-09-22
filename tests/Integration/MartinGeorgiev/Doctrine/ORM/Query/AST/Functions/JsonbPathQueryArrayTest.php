<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray;
use PHPUnit\Framework\Attributes\Test;

final class JsonbPathQueryArrayTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY_ARRAY' => JsonbPathQueryArray::class,
        ];
    }

    #[Test]
    public function returns_the_queried_values_as_an_array_from_a_json_literal(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_ARRAY(:json, :path) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'json' => '{"a": 1, "b": 2}',
            'path' => '$.b',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(1, $decoded);
        $this->assertSame(2, $decoded[0]);
    }

    #[Test]
    public function returns_the_queried_values_as_an_array_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_PATH_QUERY_ARRAY(t.jsonbObject1, :path) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, ['path' => '$.tags[*]']);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(2, $decoded);
        $this->assertSame(['developer', 'manager'], $decoded);
    }
}
