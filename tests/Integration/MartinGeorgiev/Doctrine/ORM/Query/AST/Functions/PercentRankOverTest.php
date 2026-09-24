<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentRankOver;
use PHPUnit\Framework\Attributes\Test;

final class PercentRankOverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PERCENT_RANK_OVER' => PercentRankOver::class,
        ];
    }

    #[Test]
    public function returns_the_percent_rank_from_a_numeric_literal(): void
    {
        $dql = 'SELECT PERCENT_RANK_OVER(PARTITION BY 1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_percent_rank_from_entity_fields(): void
    {
        $dql = 'SELECT PERCENT_RANK_OVER(PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }
}
