<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLtxtquery;
use PHPUnit\Framework\Attributes\Test;

final class MatchesLtxtqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_LTXTQUERY' => MatchesLtxtquery::class,
        ];
    }

    #[Test]
    public function returns_true_when_path_labels_satisfy_conjunction(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LTXTQUERY(l.ltree1, 'Top & Child2') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_true_when_path_labels_satisfy_prefix_modifier(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LTXTQUERY(l.ltree1, 'Child*') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function returns_false_when_negated_label_is_present(): void
    {
        $dql = "SELECT l.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l
                WHERE MATCHES_LTXTQUERY(l.ltree1, 'Top & !Child2') = TRUE AND l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
