<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Index;
use PHPUnit\Framework\Attributes\Test;

final class IndexTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INDEX' => Index::class,
        ];
    }

    #[Test]
    public function returns_position_of_a_subpath_from_ltree_literals(): void
    {
        $dql = "SELECT INDEX('Top.Child1.Child2', 'Top.Child1') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }

    #[Test]
    public function returns_position_of_a_subpath_from_entity_fields(): void
    {
        $dql = 'SELECT INDEX(l.ltree1, l.ltree2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }

    #[Test]
    public function returns_position_of_a_subpath_with_an_offset(): void
    {
        $dql = "SELECT INDEX(l.ltree1, 'Child1', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
