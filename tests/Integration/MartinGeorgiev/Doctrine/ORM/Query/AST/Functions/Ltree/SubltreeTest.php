<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subltree;
use PHPUnit\Framework\Attributes\Test;

final class SubltreeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SUBLTREE' => Subltree::class,
        ];
    }

    #[Test]
    public function returns_subpath_between_two_positions_from_an_ltree_literal(): void
    {
        $dql = "SELECT SUBLTREE('Top.Child1.Child2', 0, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }

    #[Test]
    public function returns_subpath_between_two_positions_from_an_entity_field(): void
    {
        $dql = 'SELECT SUBLTREE(l.ltree1, 0, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }
}
