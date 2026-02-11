<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo;
use PHPUnit\Framework\Attributes\Test;

class SimilarToTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIMILAR_TO' => SimilarTo::class,
        ];
    }

    #[Test]
    public function returns_true_when_pattern_matches(): void
    {
        $dql = "SELECT SIMILAR_TO(t.text1, '%test%') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_pattern_does_not_match(): void
    {
        $dql = "SELECT SIMILAR_TO(t.text1, '%xyz%') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function can_use_sql_regex_patterns(): void
    {
        $dql = "SELECT SIMILAR_TO(t.text1, 'this%') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
