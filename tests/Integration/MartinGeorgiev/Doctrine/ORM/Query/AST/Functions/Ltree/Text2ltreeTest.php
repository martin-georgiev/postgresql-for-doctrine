<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Text2ltree;
use PHPUnit\Framework\Attributes\Test;

final class Text2ltreeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEXT2LTREE' => Text2ltree::class,
        ];
    }

    #[Test]
    public function converts_text_to_ltree_from_a_string_literal(): void
    {
        $dql = "SELECT TEXT2LTREE('Top.Child1.Child2') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1.Child2', $result[0]['result']);
    }
}
