<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike;
use PHPUnit\Framework\Attributes\Test;

final class RegexpLikeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_LIKE' => RegexpLike::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_pattern_matches_an_entity_field(): void
    {
        $dql = "SELECT REGEXP_LIKE(t.text1, 'test.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_the_pattern_does_not_match_a_literal(): void
    {
        $dql = "SELECT REGEXP_LIKE('this is a test string', 'nonexistent.*pattern') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
