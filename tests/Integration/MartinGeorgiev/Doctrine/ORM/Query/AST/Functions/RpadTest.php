<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rpad;
use PHPUnit\Framework\Attributes\Test;

class RpadTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RPAD' => Rpad::class,
        ];
    }

    #[Test]
    public function can_right_pad_a_literal_string_with_specified_fill(): void
    {
        $dql = "SELECT RPAD('hi', 5, '0') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hi000', $result[0]['result']);
    }

    #[Test]
    public function can_right_pad_text_field_with_spaces_to_specified_length(): void
    {
        $dql = 'SELECT RPAD(t.text1, 5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo  ', $result[0]['result']);
    }
}
