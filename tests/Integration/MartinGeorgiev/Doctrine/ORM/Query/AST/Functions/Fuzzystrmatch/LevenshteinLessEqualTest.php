<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\LevenshteinLessEqual;
use PHPUnit\Framework\Attributes\Test;

class LevenshteinLessEqualTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEVENSHTEIN_LESS_EQUAL' => LevenshteinLessEqual::class,
        ];
    }

    #[Test]
    public function returns_correct_distance_when_within_max(): void
    {
        $dql = "SELECT LEVENSHTEIN_LESS_EQUAL('kitten', 'sitting', 5) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_value_greater_than_max_when_distance_exceeds_max(): void
    {
        $dql = "SELECT LEVENSHTEIN_LESS_EQUAL('kitten', 'sitting', 2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function can_use_custom_costs(): void
    {
        $dql = "SELECT LEVENSHTEIN_LESS_EQUAL('kitten', 'sitting', 1, 2, 3, 10) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(7, $result[0]['result']);
    }
}
