<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\WordSimilarity;
use PHPUnit\Framework\Attributes\Test;

class WordSimilarityTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'WORD_SIMILARITY' => WordSimilarity::class,
        ];
    }

    #[Test]
    public function returns_one_for_identical_strings(): void
    {
        $dql = "SELECT WORD_SIMILARITY('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_one_when_first_string_is_word_in_second(): void
    {
        $dql = "SELECT WORD_SIMILARITY('test', 'this is a test string') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_lower_score_for_unrelated_strings(): void
    {
        $dql = "SELECT WORD_SIMILARITY('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function can_compute_word_similarity_from_text_fields(): void
    {
        $dql = 'SELECT WORD_SIMILARITY(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertGreaterThanOrEqual(0.0, $result[0]['result']);
        $this->assertLessThanOrEqual(1.0, $result[0]['result']);
    }
}
