<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ReverseStrictWordSimilarityDistance;
use PHPUnit\Framework\Attributes\Test;

class ReverseStrictWordSimilarityDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REVERSE_STRICT_WORD_SIMILARITY_DISTANCE' => ReverseStrictWordSimilarityDistance::class,
        ];
    }

    #[Test]
    public function returns_zero_for_identical_strings(): void
    {
        $dql = "SELECT REVERSE_STRICT_WORD_SIMILARITY_DISTANCE('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_when_haystack_contains_needle_as_whole_word(): void
    {
        $dql = "SELECT REVERSE_STRICT_WORD_SIMILARITY_DISTANCE('this is a test string', 'test') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_one_for_completely_different_strings(): void
    {
        $dql = "SELECT REVERSE_STRICT_WORD_SIMILARITY_DISTANCE('xyz', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }
}
