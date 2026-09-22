<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractVersion;
use PHPUnit\Framework\Attributes\Test;

final class UuidExtractVersionTest extends NumericTestCase
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
    public function returns_the_version_from_a_literal(): void
    {
        $dql = "SELECT UUID_EXTRACT_VERSION('a0eebc99-9c0b-11d1-b465-00c04fd430c8') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);

        $this->assertEquals(1, $result[0]['result']);
    }
}
