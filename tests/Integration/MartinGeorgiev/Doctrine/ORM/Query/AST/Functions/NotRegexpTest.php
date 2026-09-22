<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp;
use PHPUnit\Framework\Attributes\Test;

final class NotRegexpTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOT_REGEXP' => NotRegexp::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_pattern_does_not_match_an_entity_field(): void
    {
        $dql = "SELECT NOT_REGEXP(t.text1, 'xyz123') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_the_pattern_matches_a_literal(): void
    {
        $dql = "SELECT NOT_REGEXP('this is a test string', 'test') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
