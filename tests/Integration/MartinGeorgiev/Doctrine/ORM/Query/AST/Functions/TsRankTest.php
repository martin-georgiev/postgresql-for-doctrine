<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRank;
use PHPUnit\Framework\Attributes\Test;

class TsRankTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'TO_TSQUERY' => ToTsquery::class,
            'TS_RANK' => TsRank::class,
        ];
    }

    #[Test]
    public function can_rank_document_against_query(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.06079271, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_non_matching_query(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR(t.text1), TO_TSQUERY('nonexistentword')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function can_rank_with_normalization_flag(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.030396355, $result[0]['result']);
    }

    #[Test]
    public function can_rank_with_language_config(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR('english', t.text1), TO_TSQUERY('english', 'lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.06079271, $result[0]['result']);
    }

    #[Test]
    public function can_rank_literal_document_against_query(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR('lorem ipsum dolor'), TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.06079271, $result[0]['result']);
    }
}
