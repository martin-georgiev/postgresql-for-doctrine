<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;
use PHPUnit\Framework\Attributes\Test;

class TsmatchTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
            'TO_TSVECTOR' => ToTsvector::class,
            'TSMATCH' => Tsmatch::class,
        ];
    }

    #[Test]
    public function returns_true_when_text_search_matches_word_in_fixture_data(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('test')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_text_search_does_not_match_fixture_data(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('nonexistent')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_searching_for_multiple_words(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('test & string')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_searching_with_or_operator(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem | nonexistent')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_searching_with_and_operator_for_missing_word(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('lorem & nonexistent')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_searching_second_text_field(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text2), TO_TSQUERY('another')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_using_english_dictionary_configuration(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR('english', t.text1), TO_TSQUERY('english', 'test')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_searching_special_characters_text(): void
    {
        $dql = "SELECT TSMATCH(TO_TSVECTOR(t.text1), TO_TSQUERY('special')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
