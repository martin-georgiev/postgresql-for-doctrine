<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith;
use PHPUnit\Framework\Attributes\Test;

final class StartsWithTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STARTS_WITH' => StartsWith::class,
        ];
    }

    #[Test]
    public function returns_whether_the_prefix_matches_from_an_entity_field(): void
    {
        $dql = "SELECT STARTS_WITH(t.text1, 'this') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_prefix_matches_from_a_text_literal(): void
    {
        $dql = "SELECT STARTS_WITH('this is a test string', 'xyz') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
