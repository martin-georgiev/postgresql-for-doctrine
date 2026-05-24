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
    public function can_decode_a_hex_encoded_literal_string(): void
    {
        $dql = "SELECT DECODE('68656c6c6f', 'hex') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }

    #[Test]
    public function can_decode_a_base64_encoded_literal_string(): void
    {
        $dql = "SELECT DECODE('aGVsbG8=', 'base64') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }
}
