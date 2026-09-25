<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rank;
use PHPUnit\Framework\Attributes\Test;

final class RankTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVER' => Over::class,
            'RANK' => Rank::class,
        ];
    }

    #[Test]
    public function returns_the_rank_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(RANK(), PARTITION BY 1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_rank_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(RANK(), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
