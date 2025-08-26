<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet;
use PHPUnit\Framework\Attributes\Test;

class JsonbSetTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET' => JsonbSet::class,
        ];
    }

    #[Test]
    public function jsonb_set_update_existing_value(): void
    {
        $dql = 'SELECT JSONB_SET(t.object1, :path, :value) as result 
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
    public function jsonb_set_add_new_value(): void
    {
        $dql = 'SELECT JSONB_SET(t.object1, :path, :value) as result 
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

    #[Test]
    public function jsonb_set_nested_path(): void
    {
        $dql = 'SELECT JSONB_SET(t.object1, :path, :value) as result 
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

    #[Test]
    public function jsonb_set_with_create_missing_false(): void
    {
        $dql = "SELECT JSONB_SET(t.object1, :path, :value, 'false') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql, [
            'path' => '{nonexistent}',
            'value' => '"value"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        self::assertArrayNotHasKey('nonexistent', $decoded);
    }
}
