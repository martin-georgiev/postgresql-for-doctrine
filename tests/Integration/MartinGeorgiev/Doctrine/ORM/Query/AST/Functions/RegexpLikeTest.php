<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike;
use PHPUnit\Framework\Attributes\Test;

class RegexpLikeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_LIKE' => RegexpLike::class,
        ];
    }

    #[Test]
    public function returns_true_when_pattern_matches_text_field(): void
    {
        $dql = "SELECT REGEXP_LIKE(t.text1, 'test.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_pattern_does_not_match(): void
    {
        $dql = "SELECT REGEXP_LIKE(t.text1, 'nonexistent.*pattern') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_matching_second_text_field(): void
    {
        $dql = "SELECT REGEXP_LIKE(t.text2, 'another.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_matching_word_boundaries(): void
    {
        // POSIX word boundary pattern: [[:<:]]is[[:>:]]
        // - [[:<:]] = start of word (word boundary at beginning)
        // - is = literal word 'is'
        // - [[:>:]] = end of word (word boundary at end)
        // This matches 'is' as a complete word, considering letters, numbers, and underscores as word characters.
        // Examples: 'this is test' ✅, 'is great' ✅, 'island' ❌, 'is_var' ❌, 'is123' ❌
        $dql = "SELECT REGEXP_LIKE(t.text1, '[[:<:]]is[[:>:]]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_matching_case_sensitive_pattern(): void
    {
        $dql = "SELECT REGEXP_LIKE(t.text1, 'TEST.*STRING') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
