<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subltree;
use PHPUnit\Framework\Attributes\Test;

class SubltreeTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SUBLTREE' => Subltree::class,
        ];
    }

    #[Test]
    public function extracts_subpath_from_ltree(): void
    {
        $dql = 'SELECT SUBLTREE(l.ltree1, 1, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Child1', $result[0]['result']);
    }

    #[Test]
    public function extracts_subpath_from_ltree_with_different_positions(): void
    {
        $dql = 'SELECT SUBLTREE(l.ltree1, 0, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }
}
