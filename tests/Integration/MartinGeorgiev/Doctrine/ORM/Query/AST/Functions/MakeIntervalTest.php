<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeInterval;
use PHPUnit\Framework\Attributes\Test;

final class MakeIntervalTest extends DateMakingTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_INTERVAL' => MakeInterval::class,
        ];
    }

    #[Test]
    public function creates_an_interval_from_literals(): void
    {
        $dql = 'SELECT MAKE_INTERVAL(1, 2, 3, 4, 5, 6, 7.5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1 year 2 mons 25 days 05:06:07.5', $result[0]['result']);
    }

    #[Test]
    public function creates_an_interval_from_entity_fields(): void
    {
        $dql = 'SELECT MAKE_INTERVAL(0, 0, 0, n.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('10 days', $result[0]['result']);
    }
}
