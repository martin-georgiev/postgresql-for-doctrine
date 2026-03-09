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
    public function calculates_similarity_from_strings(): void
    {
        $dql = "SELECT SIMILARITY('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_similarity_from_text_fields(): void
    {
        $dql = 'SELECT SIMILARITY(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.4814815, $result[0]['result']);
    }
}
