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
    public function calculates_similarity_from_strings(): void
    {
        $dql = "SELECT WORD_SIMILARITY('word', 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_similarity_from_text_fields(): void
    {
        $dql = 'SELECT WORD_SIMILARITY(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.6, $result[0]['result']);
    }
}
