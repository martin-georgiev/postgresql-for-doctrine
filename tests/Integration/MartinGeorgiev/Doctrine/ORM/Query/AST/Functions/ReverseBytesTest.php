<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReverseBytes;
use PHPUnit\Framework\Attributes\Test;

class ReverseBytesTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'reverse function for bytea');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REVERSE_BYTES' => ReverseBytes::class,
        ];
    }

    #[Test]
    public function can_reverse_a_bytea(): void
    {
        $dql = "SELECT REVERSE_BYTES('\\x1234'::bytea) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('\x3412', $result[0]['result']);
    }

    #[Test]
    public function can_reverse_text_field_as_bytea(): void
    {
        $dql = "SELECT REVERSE_BYTES(t.text1::bytea) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('\x6f6f66', $result[0]['result']);
    }
}
