<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CumeDistOver;
use PHPUnit\Framework\Attributes\Test;

final class CumeDistOverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CUME_DIST_OVER' => CumeDistOver::class,
        ];
    }

    #[Test]
    public function returns_the_cumulative_distribution_from_a_numeric_literal(): void
    {
        $dql = 'SELECT CUME_DIST_OVER(PARTITION BY 1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_cumulative_distribution_from_entity_fields(): void
    {
        $dql = 'SELECT CUME_DIST_OVER(PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }
}
