<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractVersion;
use PHPUnit\Framework\Attributes\Test;

class UuidExtractVersionTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'uuid_extract_version function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'UUID_EXTRACT_VERSION' => UuidExtractVersion::class,
        ];
    }

    #[Test]
    public function can_extract_version_from_uuid_v1(): void
    {
        $dql = "SELECT UUID_EXTRACT_VERSION('a0eebc99-9c0b-11d1-b465-00c04fd430c8') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(1, $result[0]['result']);
    }

    #[Test]
    public function can_extract_version_from_uuid_v4(): void
    {
        $dql = "SELECT UUID_EXTRACT_VERSION('550e8400-e29b-41d4-a716-446655440000') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(4, $result[0]['result']);
    }

    #[Test]
    public function can_extract_version_from_uuid_v7(): void
    {
        $dql = "SELECT UUID_EXTRACT_VERSION('018e7e39-9f42-7000-8000-000000000000') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(7, $result[0]['result']);
    }
}
