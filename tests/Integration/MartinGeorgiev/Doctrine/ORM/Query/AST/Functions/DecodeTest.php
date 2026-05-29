<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Decode;
use PHPUnit\Framework\Attributes\Test;

class DecodeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DECODE' => Decode::class,
        ];
    }

    #[Test]
    public function returns_bytea_from_hex_encoded_literal(): void
    {
        $dql = "SELECT DECODE('68656c6c6f', 'hex') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsResource($result[0]['result']);
        $this->assertSame('hello', \stream_get_contents($result[0]['result']));
    }

    #[Test]
    public function returns_bytea_from_escape_encoded_text_field(): void
    {
        $dql = "SELECT DECODE(t.text1, 'escape') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsResource($result[0]['result']);
        $this->assertSame('foo', \stream_get_contents($result[0]['result']));
    }
}
