<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ContainsWordSimilarTo;
use PHPUnit\Framework\Attributes\Test;

class ContainsWordSimilarToTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS_WORD_SIMILAR_TO' => ContainsWordSimilarTo::class,
        ];
    }

    #[Test]
    public function returns_true_for_identical_strings(): void
    {
        $dql = "SELECT CONTAINS_WORD_SIMILAR_TO('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('t', $result[0]['result']);
    }

    #[Test]
    public function returns_true_when_haystack_contains_needle_as_word(): void
    {
        $dql = "SELECT CONTAINS_WORD_SIMILAR_TO('this is a test string', 'test') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('t', $result[0]['result']);
    }

    #[Test]
    public function returns_false_for_completely_different_strings(): void
    {
        $dql = "SELECT CONTAINS_WORD_SIMILAR_TO('xyz', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('f', $result[0]['result']);
    }
}
