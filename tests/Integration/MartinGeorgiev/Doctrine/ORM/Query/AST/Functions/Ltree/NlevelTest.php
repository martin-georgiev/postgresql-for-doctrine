<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Nlevel;
use PHPUnit\Framework\Attributes\Test;

class NlevelTest extends LtreeTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NLEVEL' => Nlevel::class,
        ];
    }

    #[Test]
    public function returns_number_of_labels_in_path(): void
    {
        $dql = 'SELECT NLEVEL(l.ltree1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_number_of_labels_for_single_node(): void
    {
        $dql = 'SELECT NLEVEL(l.ltree1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsLtrees l WHERE l.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
