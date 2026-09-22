<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo;
use PHPUnit\Framework\Attributes\Test;

final class SimilarToTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIMILAR_TO' => SimilarTo::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_pattern_matches_an_entity_field(): void
    {
        $dql = "SELECT SIMILAR_TO(t.text1, '%test%') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_the_pattern_does_not_match_a_literal(): void
    {
        $dql = "SELECT SIMILAR_TO('this is a test string', '%xyz%') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
