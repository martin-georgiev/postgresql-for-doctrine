<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CharLength;
use PHPUnit\Framework\Attributes\Test;

class CharLengthTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CHAR_LENGTH' => CharLength::class,
        ];
    }

    #[Test]
    public function can_return_character_length_of_a_literal_string(): void
    {
        $dql = "SELECT CHAR_LENGTH('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function can_return_character_length_of_text_field(): void
    {
        $dql = 'SELECT CHAR_LENGTH(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
