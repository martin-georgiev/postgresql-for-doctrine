<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\Similarity;
use PHPUnit\Framework\Attributes\Test;

class SimilarityTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIMILARITY' => Similarity::class,
        ];
    }

    #[Test]
    public function returns_one_for_identical_strings(): void
    {
        $dql = "SELECT SIMILARITY('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_zero_for_completely_different_strings(): void
    {
        $dql = "SELECT SIMILARITY('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_partial_similarity_for_similar_strings(): void
    {
        $dql = "SELECT SIMILARITY('test', 'text') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertGreaterThan(0.0, $result[0]['result']);
        $this->assertLessThan(1.0, $result[0]['result']);
    }

    #[Test]
    public function can_compute_similarity_from_text_fields(): void
    {
        $dql = 'SELECT SIMILARITY(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsFloat($result[0]['result']);
        $this->assertGreaterThanOrEqual(0.0, $result[0]['result']);
        $this->assertLessThanOrEqual(1.0, $result[0]['result']);
    }
}
