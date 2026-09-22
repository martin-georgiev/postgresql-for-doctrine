<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Levenshtein;
use PHPUnit\Framework\Attributes\Test;

final class LevenshteinTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEVENSHTEIN' => Levenshtein::class,
        ];
    }

    #[Test]
    public function returns_distance_from_strings(): void
    {
        $dql = "SELECT LEVENSHTEIN('kitten', 'sitting') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_distance_from_text_fields(): void
    {
        $dql = 'SELECT LEVENSHTEIN(t.text1, t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(9, $result[0]['result']);
    }

    #[Test]
    public function returns_distance_with_custom_costs(): void
    {
        $dql = "SELECT LEVENSHTEIN('kitten', 'sitting', 1, 2, 3) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(7, $result[0]['result']);
    }
}
