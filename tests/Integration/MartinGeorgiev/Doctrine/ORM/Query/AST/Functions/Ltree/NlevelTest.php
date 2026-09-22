<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Nlevel;
use PHPUnit\Framework\Attributes\Test;

final class NlevelTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NLEVEL' => Nlevel::class,
        ];
    }

    #[Test]
    public function returns_number_of_labels_from_an_ltree_literal(): void
    {
        $dql = "SELECT NLEVEL('Top.Child1.Child2') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_number_of_labels_from_an_entity_field(): void
    {
        $dql = 'SELECT NLEVEL(l.ltree1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
