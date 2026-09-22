<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lpad;
use PHPUnit\Framework\Attributes\Test;

final class LpadTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LPAD' => Lpad::class,
        ];
    }

    #[Test]
    public function returns_the_left_padded_text_from_a_text_literal(): void
    {
        $dql = "SELECT LPAD('hi', 5, '0') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('000hi', $result[0]['result']);
    }

    #[Test]
    public function returns_the_left_padded_text_from_an_entity_field(): void
    {
        $dql = 'SELECT LPAD(t.text1, 5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('  foo', $result[0]['result']);
    }
}
