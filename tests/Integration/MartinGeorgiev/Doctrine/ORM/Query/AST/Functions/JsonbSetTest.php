<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet;
use PHPUnit\Framework\Attributes\Test;

final class JsonbSetTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET' => JsonbSet::class,
        ];
    }

    #[Test]
    public function returns_the_jsonb_with_the_value_set_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_SET(t.jsonbObject1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'path' => '{name}',
            'value' => '"John Doe"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('name', $decoded);
        $this->assertSame('John Doe', $decoded['name']);
    }

    #[Test]
    public function returns_the_jsonb_with_the_value_set_from_an_entity_field_without_creating_missing_keys(): void
    {
        $dql = "SELECT JSONB_SET(t.jsonbObject1, :path, :value, 'false') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql, [
            'path' => '{nonexistent}',
            'value' => '"value"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayNotHasKey('nonexistent', $decoded);
    }
}
