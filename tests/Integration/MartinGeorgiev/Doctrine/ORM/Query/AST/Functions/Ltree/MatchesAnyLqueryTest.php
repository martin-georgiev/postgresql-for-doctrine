<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesAnyLquery;
use PHPUnit\Framework\Attributes\Test;

final class MatchesAnyLqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_ANY_LQUERY' => MatchesAnyLquery::class,
            'ARR' => Arr::class,
        ];
    }

    #[Test]
    public function returns_true_when_path_matches_one_of_the_patterns(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_ANY_LQUERY(l.ltree1, ARR('Root.*', 'Top.*')) = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_true_when_a_pattern_carries_a_quantifier(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_ANY_LQUERY(l.ltree1, ARR('*{1,2}.Child2')) = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_false_when_no_pattern_matches(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_ANY_LQUERY(l.ltree1, ARR('Root.*', 'A.*')) = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
