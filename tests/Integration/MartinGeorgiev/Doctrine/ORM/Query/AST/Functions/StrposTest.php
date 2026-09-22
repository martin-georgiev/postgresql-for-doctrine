<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strpos;
use PHPUnit\Framework\Attributes\Test;

final class StrposTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRPOS' => Strpos::class,
        ];
    }

    #[Test]
    public function returns_the_substring_position_from_a_literal(): void
    {
        $dql = "SELECT STRPOS('hello world', 'world') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(7, $result[0]['result']);
    }

    #[Test]
    public function returns_the_substring_position_from_an_entity_field(): void
    {
        $dql = "SELECT STRPOS(t.text1, 'test') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(11, $result[0]['result']);
    }
}
