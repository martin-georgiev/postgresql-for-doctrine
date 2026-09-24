<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\LevenshteinLessEqual;
use PHPUnit\Framework\Attributes\Test;

final class LevenshteinLessEqualTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEVENSHTEIN_LESS_EQUAL' => LevenshteinLessEqual::class,
        ];
    }

    #[Test]
    public function returns_the_distance_from_text_literals(): void
    {
        $dql = "SELECT LEVENSHTEIN_LESS_EQUAL('kitten', 'sitting', 5) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_the_distance_from_entity_fields(): void
    {
        $dql = 'SELECT LEVENSHTEIN_LESS_EQUAL(t.text1, t.text2, 10) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(9, $result[0]['result']);
    }

    #[Test]
    public function returns_the_distance_with_custom_costs(): void
    {
        $dql = "SELECT LEVENSHTEIN_LESS_EQUAL('kitten', 'sitting', 1, 2, 3, 10) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(7, $result[0]['result']);
    }
}
