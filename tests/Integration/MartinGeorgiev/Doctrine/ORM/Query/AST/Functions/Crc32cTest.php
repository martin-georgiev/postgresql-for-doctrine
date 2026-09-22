<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32c;
use PHPUnit\Framework\Attributes\Test;

final class Crc32cTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'crc32c function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'CRC32C' => Crc32c::class,
        ];
    }

    #[Test]
    public function returns_the_crc32c_checksum_from_a_text_literal(): void
    {
        $dql = "SELECT CRC32C('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(3502610633, $result[0]['result']);
    }

    #[Test]
    public function returns_the_crc32c_checksum_from_an_entity_field(): void
    {
        $dql = 'SELECT CRC32C(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(767721578, $result[0]['result']);
    }
}
