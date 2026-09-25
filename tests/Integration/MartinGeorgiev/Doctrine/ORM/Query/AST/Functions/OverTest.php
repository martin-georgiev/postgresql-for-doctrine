<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class OverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_windowed_aggregate_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(SUM(n.integer1), PARTITION BY 1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_windowed_aggregate_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(SUM(n.integer1), PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_windowed_aggregate_with_a_frame(): void
    {
        $dql = 'SELECT OVER(SUM(n.integer1), PARTITION BY n.integer1 ORDER BY n.integer2 ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
