<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strpos;
use PHPUnit\Framework\Attributes\Test;

class StrposTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRPOS' => Strpos::class,
        ];
    }

    #[Test]
    public function can_find_position_of_substring_in_a_literal_string(): void
    {
        $dql = "SELECT STRPOS('hello world', 'world') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(7, $result[0]['result']);
    }

    #[Test]
    public function can_find_position_of_substring_in_text_field(): void
    {
        $dql = "SELECT STRPOS(t.text1, 'test') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(11, $result[0]['result']);
    }
}
