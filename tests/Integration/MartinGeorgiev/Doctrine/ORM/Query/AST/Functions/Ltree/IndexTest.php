<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Index;
use PHPUnit\Framework\Attributes\Test;

class IndexTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INDEX' => Index::class,
        ];
    }

    #[Test]
    public function can_find_position_of_ltree_in_another_ltree(): void
    {
        $dql = 'SELECT INDEX(l.ltree1, l.ltree2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }

    #[Test]
    public function returns_negative_one_when_not_found(): void
    {
        $dql = 'SELECT INDEX(l.ltree2, l.ltree3) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(-1, $result[0]['result']);
    }

    #[Test]
    public function finds_position_with_offset(): void
    {
        $dql = "SELECT INDEX(l.ltree1, 'Child1', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function finds_position_with_negative_offset(): void
    {
        $dql = "SELECT INDEX(l.ltree1, 'Child1', -2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
