<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Ltree2text;
use PHPUnit\Framework\Attributes\Test;

class Ltree2textTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LTREE2TEXT' => Ltree2text::class,
        ];
    }

    #[Test]
    public function can_cast_ltree_to_text(): void
    {
        $dql = 'SELECT LTREE2TEXT(l.ltree1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Top.Child1.Child2', $result[0]['result']);
    }
}
