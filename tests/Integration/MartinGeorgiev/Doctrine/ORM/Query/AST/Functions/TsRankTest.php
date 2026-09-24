<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRank;
use PHPUnit\Framework\Attributes\Test;

final class TsRankTest extends TextTestCase
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
    public function returns_a_rank_from_an_entity_field(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.06079271, $result[0]['result']);
    }

    #[Test]
    public function returns_a_rank_with_a_normalization(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.030396355, $result[0]['result']);
    }

    #[Test]
    public function returns_a_rank_with_weights_and_normalization(): void
    {
        $dql = "SELECT TS_RANK('{1,1,1,1}', TO_TSVECTOR(t.text1), TO_TSQUERY('lorem'), 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.30396354, $result[0]['result']);
    }

    #[Test]
    public function returns_a_rank_from_a_literal(): void
    {
        $dql = "SELECT TS_RANK(TO_TSVECTOR('lorem ipsum dolor'), TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.06079271, $result[0]['result']);
    }
}
