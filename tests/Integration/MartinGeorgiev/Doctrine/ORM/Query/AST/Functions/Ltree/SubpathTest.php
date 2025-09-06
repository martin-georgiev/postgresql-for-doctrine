<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subpath;
use PHPUnit\Framework\Attributes\Test;

class SubpathTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SUBPATH' => Subpath::class,
        ];
    }

    #[Test]
    public function can_extract_with_offset_and_length(): void
    {
        $dql = 'SELECT SUBPATH(l.ltree1, 0, 2) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }

    #[Test]
    public function can_extract_with_offset_only(): void
    {
        $dql = 'SELECT SUBPATH(l.ltree1, 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Child1.Child2', $result[0]['result']);
    }

    #[Test]
    public function can_extract_with_negative_offset(): void
    {
        $dql = 'SELECT SUBPATH(l.ltree1, -1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Child2', $result[0]['result']);
    }

    #[Test]
    public function can_extract_with_negative_length(): void
    {
        $dql = 'SELECT SUBPATH(l.ltree1, 0, -1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1', $result[0]['result']);
    }
}
