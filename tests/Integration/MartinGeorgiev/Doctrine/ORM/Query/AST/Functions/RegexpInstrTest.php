<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr;
use PHPUnit\Framework\Attributes\Test;

class RegexpInstrTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_INSTR' => RegexpInstr::class,
        ];
    }

    #[Test]
    public function returns_position_when_finding_matching_pattern(): void
    {
        $dql = "SELECT REGEXP_INSTR(t.text1, 'test') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(11, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_non_matching_pattern(): void
    {
        $dql = "SELECT REGEXP_INSTR(t.text1, 'nonexistent') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }

    #[Test]
    public function returns_position_when_finding_pattern_in_second_text_field(): void
    {
        $dql = "SELECT REGEXP_INSTR(t.text2, 'test') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(9, $result[0]['result']);
    }

    #[Test]
    public function returns_position_when_finding_word_boundary_pattern(): void
    {
        // POSIX word boundary pattern: [[:<:]]is[[:>:]]
        // - [[:<:]] = start of word (word boundary at beginning)
        // - is = literal word 'is'
        // - [[:>:]] = end of word (word boundary at end)
        // This matches 'is' as a complete word, considering letters, numbers, and underscores as word characters.
        // Returns position 6 (start of 'is') in 'this is a test string'
        $dql = "SELECT REGEXP_INSTR(t.text1, '[[:<:]]is[[:>:]]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(6, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_case_sensitive_pattern(): void
    {
        $dql = "SELECT REGEXP_INSTR(t.text1, 'TEST') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }
}
