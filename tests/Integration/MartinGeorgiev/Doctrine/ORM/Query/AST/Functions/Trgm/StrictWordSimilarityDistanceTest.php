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
    public function returns_zero_when_needle_is_whole_word_in_haystack(): void
    {
        $dql = "SELECT STRICT_WORD_SIMILARITY_DISTANCE('test', 'this is a test string') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_distance_from_entity_fields(): void
    {
        $dql = 'SELECT STRICT_WORD_SIMILARITY_DISTANCE(t.text1, t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }
}
