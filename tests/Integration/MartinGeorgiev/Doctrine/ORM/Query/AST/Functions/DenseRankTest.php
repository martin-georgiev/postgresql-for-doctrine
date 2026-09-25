<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DenseRank;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class DenseRankTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DENSE_RANK' => DenseRank::class,
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_dense_rank_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(DENSE_RANK(), PARTITION BY 1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_dense_rank_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(DENSE_RANK(), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
