<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractTimestamp;
use PHPUnit\Framework\Attributes\Test;

class UuidExtractTimestampTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'uuid_extract_timestamp function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'UUID_EXTRACT_TIMESTAMP' => UuidExtractTimestamp::class,
        ];
    }

    #[Test]
    public function can_extract_timestamp_from_uuid_v1(): void
    {
        $dql = "SELECT UUID_EXTRACT_TIMESTAMP('a0eebc99-9c0b-11d1-b465-00c04fd430c8') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $timestamp = $result[0]['result'];
        \assert(\is_string($timestamp));

        $this->assertStringStartsWith('1998-02-02 20:23:12.90287', $timestamp);
    }

    #[Test]
    public function can_extract_timestamp_from_uuid_v7(): void
    {
        $dql = "SELECT UUID_EXTRACT_TIMESTAMP('018e7e39-9f42-7000-8000-000000000000') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $timestamp = $result[0]['result'];
        \assert(\is_string($timestamp));

        $this->assertStringStartsWith('2024-03-27 04:44:49.346', $timestamp);
    }

    #[Test]
    public function returns_null_for_non_timestamped_uuid(): void
    {
        $dql = "SELECT UUID_EXTRACT_TIMESTAMP('550e8400-e29b-41d4-a716-446655440000') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertNull($result[0]['result']);
    }
}
