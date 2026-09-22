<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyInterval;
use PHPUnit\Framework\Attributes\Test;

final class JustifyIntervalTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JUSTIFY_INTERVAL' => JustifyInterval::class,
        ];
    }

    #[Test]
    public function returns_the_justified_interval_from_an_interval_literal(): void
    {
        $dql = "SELECT JUSTIFY_INTERVAL('1 mon -1 hour') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('29 days 23:00:00', $result[0]['result']);
    }

    #[Test]
    public function returns_the_justified_interval_from_an_entity_field(): void
    {
        $dql = 'SELECT JUSTIFY_INTERVAL(t.dateinterval1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('15:02:12', $result[0]['result']);
    }
}
