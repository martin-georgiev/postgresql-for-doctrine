<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\SimilarityDistance;
use PHPUnit\Framework\Attributes\Test;

class SimilarityDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIMILARITY_DISTANCE' => SimilarityDistance::class,
        ];
    }

    #[Test]
    public function returns_zero_for_identical_strings(): void
    {
        $dql = "SELECT SIMILARITY_DISTANCE('word', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_one_for_completely_different_strings(): void
    {
        $dql = "SELECT SIMILARITY_DISTANCE('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_partial_distance_for_similar_strings(): void
    {
        $dql = "SELECT SIMILARITY_DISTANCE('test', 'text') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.75, $result[0]['result']);
    }
}
