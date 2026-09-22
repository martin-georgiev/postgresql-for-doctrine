<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Right;
use PHPUnit\Framework\Attributes\Test;

final class RightTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RIGHT' => Right::class,
        ];
    }

    #[Test]
    public function returns_the_rightmost_characters_from_an_entity_field(): void
    {
        $dql = 'SELECT RIGHT(t.text1, 6) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('string', $result[0]['result']);
    }

    #[Test]
    public function returns_the_rightmost_characters_from_a_literal(): void
    {
        $dql = "SELECT RIGHT('this is a test string', 6) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('string', $result[0]['result']);
    }
}
