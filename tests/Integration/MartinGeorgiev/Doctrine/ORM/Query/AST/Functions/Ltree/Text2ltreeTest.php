<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Text2ltree;
use PHPUnit\Framework\Attributes\Test;

class Text2ltreeTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEXT2LTREE' => Text2ltree::class,
        ];
    }

    #[Test]
    public function casts_text_to_ltree(): void
    {
        $dql = "SELECT TEXT2LTREE('Top.Child1.Child2') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1.Child2', $result[0]['result']);
    }

    #[Test]
    public function casts_single_node_text_to_ltree(): void
    {
        $dql = "SELECT TEXT2LTREE('Root') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Root', $result[0]['result']);
    }

    #[Test]
    public function casts_empty_text_to_ltree(): void
    {
        $dql = "SELECT TEXT2LTREE('') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }
}
