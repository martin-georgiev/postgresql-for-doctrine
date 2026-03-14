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
    public function calculates_distance_from_strings(): void
    {
        $dql = "SELECT SIMILARITY_DISTANCE('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_distance_from_text_fields(): void
    {
        $dql = 'SELECT SIMILARITY_DISTANCE(t.text1, t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.5185185, $result[0]['result']);
    }
}
