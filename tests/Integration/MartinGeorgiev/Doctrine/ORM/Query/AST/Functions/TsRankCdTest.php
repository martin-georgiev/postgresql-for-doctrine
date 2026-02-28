<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRankCd;
use PHPUnit\Framework\Attributes\Test;

class TsRankCdTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'TO_TSQUERY' => ToTsquery::class,
            'TS_RANK_CD' => TsRankCd::class,
        ];
    }

    #[Test]
    public function can_rank_document_using_cover_density(): void
    {
        $dql = "SELECT TS_RANK_CD(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.1, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_non_matching_query(): void
    {
        $dql = "SELECT TS_RANK_CD(TO_TSVECTOR(t.text1), TO_TSQUERY('nonexistentword')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function can_rank_with_normalization_flag(): void
    {
        $dql = "SELECT TS_RANK_CD(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.072134756, $result[0]['result']);
    }

    #[Test]
    public function can_rank_with_weights_and_normalization(): void
    {
        $dql = "SELECT TS_RANK_CD('{1,1,1,1}', TO_TSVECTOR(t.text1), TO_TSQUERY('lorem'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.7213475, $result[0]['result']);
    }

    #[Test]
    public function can_rank_literal_document_using_cover_density(): void
    {
        $dql = "SELECT TS_RANK_CD(TO_TSVECTOR('lorem ipsum dolor'), TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.1, $result[0]['result']);
    }
}
