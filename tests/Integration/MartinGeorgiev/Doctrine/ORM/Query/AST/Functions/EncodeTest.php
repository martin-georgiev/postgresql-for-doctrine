<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Encode;
use PHPUnit\Framework\Attributes\Test;

final class EncodeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
            'ENCODE' => Encode::class,
        ];
    }

    #[Test]
    public function returns_the_encoded_text_from_a_text_literal(): void
    {
        $dql = "SELECT ENCODE('hello', 'hex') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('68656c6c6f', $result[0]['result']);
    }

    #[Test]
    public function returns_the_encoded_text_from_an_entity_field(): void
    {
        $dql = "SELECT ENCODE(CAST(t.text1 AS bytea), 'hex') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('666f6f', $result[0]['result']);
    }
}
