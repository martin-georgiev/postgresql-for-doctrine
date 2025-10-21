<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32c;
use PHPUnit\Framework\Attributes\Test;

class Crc32cTest extends TextTestCase
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
    public function can_compute_crc32c_of_a_string(): void
    {
        $dql = "SELECT CRC32C('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(1499930675, $result[0]['result']);
    }

    #[Test]
    public function can_compute_crc32c_of_text_field(): void
    {
        $dql = 'SELECT CRC32C(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2666930069, $result[0]['result']);
    }
}
