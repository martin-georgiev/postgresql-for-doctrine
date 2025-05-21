<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert;

class JsonbInsertTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_INSERT' => JsonbInsert::class,
        ];
    }

    public function test_jsonb_insert_new_value(): void
    {
        $dql = 'SELECT JSONB_INSERT(t.object1, :path, :value) as result 
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

    public function test_jsonb_insert_nested_path(): void
    {
        $dql = 'SELECT JSONB_INSERT(t.object1, :path, :value) as result 
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

    public function test_throws_exception_when_inserting_at_existing_object_key(): void
    {
        $this->expectException(Exception::class);
        $dql = 'SELECT JSONB_INSERT(t.object1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $this->executeDqlQuery($dql, [
            'path' => '{name}',
            'value' => '"John Doe"',
        ]);
    }

    public function test_throws_exception_when_inserting_at_existing_nested_path(): void
    {
        $this->expectException(Exception::class);

        $dql = 'SELECT JSONB_INSERT(t.object1, :path, :value) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5';

        $this->executeDqlQuery($dql, [
            'path'  => '{address,zip}',
            'value' => '"10001"',
        ]);
    }
}
