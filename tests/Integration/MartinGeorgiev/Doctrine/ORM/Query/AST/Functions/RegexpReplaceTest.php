<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace;
use PHPUnit\Framework\Attributes\Test;

class RegexpReplaceTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_REPLACE' => RegexpReplace::class,
        ];
    }

    #[Test]
    public function replaces_matching_pattern_in_text_field(): void
    {
        $dql = "SELECT REGEXP_REPLACE(t.text1, 'test', 'replaced') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a replaced string', $result[0]['result']);
    }

    #[Test]
    public function replaces_multiple_occurrences_with_global_flag(): void
    {
        $dql = "SELECT REGEXP_REPLACE(t.text1, 'is', 'was', 'g') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('thwas was a test string', $result[0]['result']);
    }

    #[Test]
    public function leaves_string_unchanged_when_no_match(): void
    {
        $dql = "SELECT REGEXP_REPLACE(t.text1, 'nonexistent', 'replaced') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string', $result[0]['result']);
    }

    #[Test]
    public function replaces_pattern_in_second_text_field(): void
    {
        $dql = "SELECT REGEXP_REPLACE(t.text2, 'test', 'replaced') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('another replaced string', $result[0]['result']);
    }

    #[Test]
    public function handles_case_sensitive_replacement(): void
    {
        $dql = "SELECT REGEXP_REPLACE(t.text1, 'TEST', 'replaced') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string', $result[0]['result']);
    }
}
