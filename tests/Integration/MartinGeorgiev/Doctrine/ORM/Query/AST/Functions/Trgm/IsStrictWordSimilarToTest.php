<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\IsStrictWordSimilarTo;
use PHPUnit\Framework\Attributes\Test;

class IsStrictWordSimilarToTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IS_STRICT_WORD_SIMILAR_TO' => IsStrictWordSimilarTo::class,
        ];
    }

    #[Test]
    public function returns_true_when_needle_is_whole_word_in_haystack_entity_field(): void
    {
        $dql = "SELECT t.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE IS_STRICT_WORD_SIMILAR_TO('test', t.text1) = TRUE AND t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_false_when_entity_fields_share_no_similar_words(): void
    {
        $dql = 'SELECT t.id FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE IS_STRICT_WORD_SIMILAR_TO(t.text2, t.text1) = TRUE AND t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
