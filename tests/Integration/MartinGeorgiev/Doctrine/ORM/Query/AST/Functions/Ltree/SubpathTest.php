<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subpath;
use PHPUnit\Framework\Attributes\Test;

final class SubpathTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SUBPATH' => Subpath::class,
        ];
    }

    #[Test]
    public function returns_subpath_from_an_ltree_literal(): void
    {
        $dql = "SELECT SUBPATH('Top.Child1.Child2', 0, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }

    #[Test]
    public function returns_subpath_from_an_entity_field(): void
    {
        $dql = 'SELECT SUBPATH(l.ltree1, 0, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }

    #[Test]
    public function returns_subpath_without_a_length_argument(): void
    {
        $dql = 'SELECT SUBPATH(l.ltree1, 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Child1.Child2', $result[0]['result']);
    }
}
