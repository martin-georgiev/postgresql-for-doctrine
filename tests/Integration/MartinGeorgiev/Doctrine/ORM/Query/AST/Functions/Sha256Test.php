<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha256;
use PHPUnit\Framework\Attributes\Test;

final class Sha256Test extends TextTestCase
{
    use ByteaAssertionTrait;

    protected function getStringFunctions(): array
    {
        return [
            'SHA256' => Sha256::class,
        ];
    }

    #[Test]
    public function returns_the_sha256_hash_from_a_literal(): void
    {
        $dql = "SELECT SHA256('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertByteaEquals('d750212353191efa677931636671789003eb7229e5a1f003e851b213aa5cb8a6', $result[0]['result']);
    }

    #[Test]
    public function returns_the_sha256_hash_from_an_entity_field(): void
    {
        $dql = 'SELECT SHA256(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertByteaEquals('f6774519d1c7a3389ef327e9c04766b999db8cdfb85d1346c471ee86d65885bc', $result[0]['result']);
    }
}
