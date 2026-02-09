<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Difference;
use PHPUnit\Framework\Attributes\Test;

class DifferenceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DIFFERENCE' => Difference::class,
        ];
    }

    #[Test]
    public function returns_four_for_exact_match(): void
    {
        $dql = "SELECT DIFFERENCE('Anne', 'Anne') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(4, $result[0]['result']);
    }

    #[Test]
    public function returns_similarity_score_for_similar_names(): void
    {
        $dql = "SELECT DIFFERENCE('Anne', 'Ann') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(4, $result[0]['result']);
    }

    #[Test]
    public function returns_similarity_score_for_not_so_similar_names(): void
    {
        $dql = "SELECT DIFFERENCE('Anne', 'Anton') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_lower_score_for_different_names(): void
    {
        $dql = "SELECT DIFFERENCE('Anne', 'Margaret') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }
}
