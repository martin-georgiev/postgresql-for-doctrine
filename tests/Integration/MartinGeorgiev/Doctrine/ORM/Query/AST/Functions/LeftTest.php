<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Left;
use PHPUnit\Framework\Attributes\Test;

final class LeftTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEFT' => Left::class,
        ];
    }

    #[Test]
    public function returns_the_leading_characters_from_an_entity_field(): void
    {
        $dql = 'SELECT LEFT(t.text1, 4) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this', $result[0]['result']);
    }

    #[Test]
    public function returns_the_leading_characters_from_a_text_literal(): void
    {
        $dql = "SELECT LEFT('this is a test string', 4) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this', $result[0]['result']);
    }
}
