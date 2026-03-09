<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\StrictWordSimilarity;
use PHPUnit\Framework\Attributes\Test;

class StrictWordSimilarityTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICT_WORD_SIMILARITY' => StrictWordSimilarity::class,
        ];
    }

    #[Test]
    public function returns_one_for_identical_strings(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_one_when_first_string_is_whole_word_in_second(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY('test', 'this is a test string') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_lower_score_for_unrelated_strings(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function can_compute_strict_word_similarity_from_text_fields(): void
    {
        $dql = 'SELECT STRICT_WORD_SIMILARITY(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertGreaterThanOrEqual(0.0, $result[0]['result']);
        $this->assertLessThanOrEqual(1.0, $result[0]['result']);
    }
}
