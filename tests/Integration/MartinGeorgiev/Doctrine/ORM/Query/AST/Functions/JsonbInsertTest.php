<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert;
use PHPUnit\Framework\Attributes\Test;

final class JsonbInsertTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_INSERT' => JsonbInsert::class,
        ];
    }

    #[Test]
    public function returns_the_jsonb_with_the_inserted_value_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_INSERT(t.jsonbObject1, :path, :value) as result 
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
    public function rejects_inserting_at_an_existing_key(): void
    {
        $this->expectException(Exception::class);
        $dql = 'SELECT JSONB_INSERT(t.jsonbObject1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $this->executeDqlQuery($dql, [
            'path' => '{name}',
            'value' => '"John Doe"',
        ]);
    }
}
