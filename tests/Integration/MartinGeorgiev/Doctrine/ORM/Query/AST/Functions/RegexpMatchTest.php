<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch;
use PHPUnit\Framework\Attributes\Test;

class RegexpMatchTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_MATCH' => RegexpMatch::class,
        ];
    }

    #[Test]
    public function can_match_pattern_in_text(): void
    {
        $dql = "SELECT REGEXP_MATCH(t.text1, 'test') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('test', $result[0]['result']);
    }

    #[Test]
    public function returns_null_when_no_match(): void
    {
        $dql = "SELECT REGEXP_MATCH(t.text1, 'xyz123') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function can_use_case_insensitive_flag(): void
    {
        $dql = "SELECT REGEXP_MATCH(t.text1, 'TEST', 'i') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('test', $result[0]['result']);
    }
}
