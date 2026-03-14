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
    public function calculates_distance_from_strings(): void
    {
        $dql = "SELECT REVERSE_STRICT_WORD_SIMILARITY_DISTANCE('xyz', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_distance_from_text_fields(): void
    {
        $dql = 'SELECT REVERSE_STRICT_WORD_SIMILARITY_DISTANCE(t.text2, t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.4, $result[0]['result'], 0.0001);
    }
}
