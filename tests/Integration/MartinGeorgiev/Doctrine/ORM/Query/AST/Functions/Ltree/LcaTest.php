<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Lca;
use PHPUnit\Framework\Attributes\Test;

final class LcaTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LCA' => Lca::class,
        ];
    }

    #[Test]
    public function returns_longest_common_ancestor_from_ltree_literals(): void
    {
        $dql = "SELECT LCA('Top.Child1.Child2', 'Top.Child1') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top', $result[0]['result']);
    }

    #[Test]
    public function returns_longest_common_ancestor_from_entity_fields(): void
    {
        $dql = 'SELECT LCA(l.ltree1, l.ltree2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top', $result[0]['result']);
    }

    #[Test]
    public function returns_longest_common_ancestor_of_four_paths(): void
    {
        $dql = "SELECT LCA(l.ltree1, l.ltree2, l.ltree3, '1.2.3.456') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1.2.3', $result[0]['result']);
    }
}
