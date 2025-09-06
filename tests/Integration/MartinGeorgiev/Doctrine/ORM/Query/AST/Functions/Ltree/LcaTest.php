<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Lca;
use PHPUnit\Framework\Attributes\Test;

class LcaTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LCA' => Lca::class,
        ];
    }

    #[Test]
    public function can_compute_longest_common_ancestor_of_two_paths(): void
    {
        $dql = 'SELECT LCA(l.ltree1, l.ltree2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top', $result[0]['result']);
    }

    #[Test]
    public function can_compute_longest_common_ancestor_of_three_paths(): void
    {
        $dql = 'SELECT LCA(l.ltree1, l.ltree2, l.ltree3, \'1.2.3.456\') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1.2.3', $result[0]['result']);
    }

    #[Test]
    public function can_compute_longest_common_ancestor_to_be_empty_string_when_one_of_the_paths_has_only_a_root_with_no_leafs(): void
    {
        $dql = 'SELECT LCA(l.ltree1, l.ltree2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }

    #[Test]
    public function can_compute_longest_common_ancestor_with_string_literals(): void
    {
        $dql = "SELECT LCA('Top.Child1.Child2', 'Top.Child1', 'Top.Child2.Child3') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top', $result[0]['result']);
    }
}
