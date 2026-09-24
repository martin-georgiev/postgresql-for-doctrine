<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ReverseWordSimilarityDistance;
use PHPUnit\Framework\Attributes\Test;

final class ReverseWordSimilarityDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REVERSE_WORD_SIMILARITY_DISTANCE' => ReverseWordSimilarityDistance::class,
        ];
    }

    #[Test]
    public function returns_the_distance_from_text_literals(): void
    {
        $dql = "SELECT REVERSE_WORD_SIMILARITY_DISTANCE('xyz', 'word') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_distance_from_entity_fields(): void
    {
        $dql = 'SELECT REVERSE_WORD_SIMILARITY_DISTANCE(t.text2, t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.39999998, $result[0]['result']);
    }
}
