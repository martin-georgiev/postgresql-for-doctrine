<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax;

class JsonbSetLaxTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET_LAX' => JsonbSetLax::class,
        ];
    }

    public function test_jsonb_set_lax_update_existing_value(): void
    {
        $dql = 'SELECT JSONB_SET_LAX(t.object1, :path, :value) as result 
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

    public function test_jsonb_set_lax_add_new_value(): void
    {
        $dql = 'SELECT JSONB_SET_LAX(t.object1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'path' => '{email}',
            'value' => '"john@example.com"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('email', $decoded);
        $this->assertSame('john@example.com', $decoded['email']);
    }

    public function test_jsonb_set_lax_nested_path(): void
    {
        $dql = 'SELECT JSONB_SET_LAX(t.object1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'path' => '{address,zip}',
            'value' => '"10001"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('address', $decoded);
        $this->assertIsArray($decoded['address']);
        $this->assertArrayHasKey('zip', $decoded['address']);
        $this->assertSame('10001', $decoded['address']['zip']);
    }

    public function test_jsonb_set_lax_with_invalid_path(): void
    {
        $dql = 'SELECT JSONB_SET_LAX(t.object1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'path' => '{invalid,path}',
            'value' => '"value"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        self::assertArrayNotHasKey('invalid', $decoded);
    }
}
