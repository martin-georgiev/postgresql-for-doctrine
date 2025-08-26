<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp;
use PHPUnit\Framework\Attributes\Test;

class IRegexpTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IREGEXP' => IRegexp::class,
        ];
    }

    #[Test]
    public function returns_true_when_pattern_matches_text_field(): void
    {
        $dql = "SELECT IREGEXP(t.text1, 'test.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_pattern_matches_with_case_insensitive(): void
    {
        $dql = "SELECT IREGEXP(t.text1, 'TEST.*STRING') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_pattern_does_not_match(): void
    {
        $dql = "SELECT IREGEXP(t.text1, 'nonexistent.*pattern') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_matching_second_text_field(): void
    {
        $dql = "SELECT IREGEXP(t.text2, 'another.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_matching_word_boundaries(): void
    {
        $dql = "SELECT IREGEXP(t.text1, '\\bis\\b') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsBool($result[0]['result']);
        $this->assertTrue($result[0]['result']);
    }
}
