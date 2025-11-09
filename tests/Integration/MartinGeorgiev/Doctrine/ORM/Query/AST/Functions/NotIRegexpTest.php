<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp;
use PHPUnit\Framework\Attributes\Test;

class NotIRegexpTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOT_IREGEXP' => NotIRegexp::class,
        ];
    }

    #[Test]
    public function returns_true_when_pattern_does_not_match_text_field(): void
    {
        $dql = "SELECT NOT_IREGEXP(t.text1, 'nonexistent.*pattern') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_pattern_matches_text_field(): void
    {
        $dql = "SELECT NOT_IREGEXP(t.text1, 'test.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_pattern_does_not_match_with_case_insensitive(): void
    {
        $dql = "SELECT NOT_IREGEXP(t.text1, 'TEST.*STRING') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_pattern_does_not_match_second_text_field(): void
    {
        $dql = "SELECT NOT_IREGEXP(t.text2, 'nonexistent.*pattern') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_pattern_matches_second_text_field(): void
    {
        $dql = "SELECT NOT_IREGEXP(t.text2, 'another.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
