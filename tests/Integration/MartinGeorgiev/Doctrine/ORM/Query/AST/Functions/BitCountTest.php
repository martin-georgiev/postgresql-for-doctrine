<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitCount;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Decode;
use PHPUnit\Framework\Attributes\Test;

final class BitCountTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_COUNT' => BitCount::class,
            'DECODE' => Decode::class,
        ];
    }

    #[Test]
    public function returns_the_set_bit_count_from_a_literal(): void
    {
        $dql = "SELECT BIT_COUNT(DECODE('ff', 'hex')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(8, $result[0]['result']);
    }

    #[Test]
    public function returns_the_set_bit_count_from_an_entity_field(): void
    {
        $dql = "SELECT BIT_COUNT(DECODE(t.text1, 'escape')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(76, $result[0]['result']);
    }
}
