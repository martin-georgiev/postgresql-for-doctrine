<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ContainsWordSimilarTo;
use PHPUnit\Framework\Attributes\Test;

class ContainsWordSimilarToTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS_WORD_SIMILAR_TO' => ContainsWordSimilarTo::class,
        ];
    }

    #[Test]
    public function returns_true_when_haystack_entity_field_contains_needle_as_word(): void
    {
        $dql = "SELECT t.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE CONTAINS_WORD_SIMILAR_TO(t.text1, 'test') = TRUE AND t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_false_when_entity_fields_share_no_similar_words(): void
    {
        $dql = 'SELECT t.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE CONTAINS_WORD_SIMILAR_TO(t.text1, t.text2) = TRUE AND t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
