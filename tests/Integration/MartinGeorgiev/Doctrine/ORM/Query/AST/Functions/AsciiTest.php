<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ascii;
use PHPUnit\Framework\Attributes\Test;

class AsciiTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASCII' => Ascii::class,
        ];
    }

    #[Test]
    public function returns_ascii_code_of_literal_character(): void
    {
        $dql = "SELECT ASCII('A') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(65, $result[0]['result']);
    }

    #[Test]
    public function returns_ascii_code_of_text_field(): void
    {
        $dql = 'SELECT ASCII(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(116, $result[0]['result']);
    }
}
