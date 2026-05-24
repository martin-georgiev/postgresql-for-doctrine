<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Encode;
use PHPUnit\Framework\Attributes\Test;

class EncodeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ENCODE' => Encode::class,
        ];
    }

    #[Test]
    public function can_encode_a_literal_bytea_value_as_hex(): void
    {
        $dql = "SELECT ENCODE('\\\\x68656c6c6f', 'hex') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }

    #[Test]
    public function can_encode_a_literal_bytea_value_as_base64(): void
    {
        $dql = "SELECT ENCODE('\\\\x68656c6c6f', 'base64') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }
}
