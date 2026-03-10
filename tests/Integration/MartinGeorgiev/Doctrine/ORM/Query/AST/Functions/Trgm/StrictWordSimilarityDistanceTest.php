<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\StrictWordSimilarityDistance;
use PHPUnit\Framework\Attributes\Test;

class StrictWordSimilarityDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICT_WORD_SIMILARITY_DISTANCE' => StrictWordSimilarityDistance::class,
        ];
    }

    #[Test]
    public function returns_zero_for_identical_strings(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY_DISTANCE('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_when_needle_is_whole_word_in_haystack(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY_DISTANCE('test', 'this is a test string') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_one_for_completely_different_strings(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY_DISTANCE('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }
}
