<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLquery;
use PHPUnit\Framework\Attributes\Test;

final class MatchesLqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_LQUERY' => MatchesLquery::class,
        ];
    }

    #[Test]
    public function returns_true_when_path_matches_prefix_pattern(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LQUERY(l.ltree1, 'Top.*') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_true_when_path_matches_quantified_star_pattern(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LQUERY(l.ltree1, '*{1,2}.Child2') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_false_when_path_does_not_match_pattern(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LQUERY(l.ltree1, 'Root.*') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
