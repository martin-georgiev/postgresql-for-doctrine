<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32;
use PHPUnit\Framework\Attributes\Test;

class Crc32Test extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'crc32 function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'CRC32' => Crc32::class,
        ];
    }

    #[Test]
    public function can_compute_crc32_of_a_string(): void
    {
        $dql = "SELECT CRC32('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2251378310, $result[0]['result']);
    }

    #[Test]
    public function can_compute_crc32_of_text_field(): void
    {
        $dql = 'SELECT CRC32(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
        $this->assertSame(2948041025, $result[0]['result']);
    }
}
