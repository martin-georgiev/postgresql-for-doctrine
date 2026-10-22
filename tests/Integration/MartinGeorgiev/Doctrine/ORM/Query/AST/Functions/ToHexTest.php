<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToHex;
use PHPUnit\Framework\Attributes\Test;

final class ToHexTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_HEX' => ToHex::class,
        ];
    }

    #[Test]
    public function returns_the_hexadecimal_representation_from_a_numeric_literal(): void
    {
        $dql = 'SELECT TO_HEX(255) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('ff', $result[0]['result']);
    }

    #[Test]
    public function returns_the_hexadecimal_representation_from_an_entity_field(): void
    {
        $dql = 'SELECT TO_HEX(n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('a', $result[0]['result']);
    }
}
