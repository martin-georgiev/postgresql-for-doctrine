<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\IsStrictWordSimilarTo;
use PHPUnit\Framework\Attributes\Test;

class IsStrictWordSimilarToTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IS_STRICT_WORD_SIMILAR_TO' => IsStrictWordSimilarTo::class,
        ];
    }

    #[Test]
    public function returns_true_for_identical_strings(): void
    {
        $dql = "SELECT IS_STRICT_WORD_SIMILAR_TO('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('t', $result[0]['result']);
    }

    #[Test]
    public function returns_true_when_needle_is_whole_word_in_haystack(): void
    {
        $dql = "SELECT IS_STRICT_WORD_SIMILAR_TO('test', 'this is a test string') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('t', $result[0]['result']);
    }

    #[Test]
    public function returns_false_for_completely_different_strings(): void
    {
        $dql = "SELECT IS_STRICT_WORD_SIMILAR_TO('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('f', $result[0]['result']);
    }
}
