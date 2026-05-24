<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lpad;
use PHPUnit\Framework\Attributes\Test;

class LpadTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LPAD' => Lpad::class,
        ];
    }

    #[Test]
    public function can_left_pad_a_literal_string_with_specified_fill(): void
    {
        $dql = "SELECT LPAD('hi', 5, '0') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('000hi', $result[0]['result']);
    }

    #[Test]
    public function can_left_pad_text_field_with_spaces_to_specified_length(): void
    {
        $dql = 'SELECT LPAD(t.text1, 5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('  foo', $result[0]['result']);
    }
}
