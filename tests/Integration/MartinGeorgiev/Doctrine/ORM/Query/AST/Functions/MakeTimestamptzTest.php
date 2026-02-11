<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamptz;
use PHPUnit\Framework\Attributes\Test;

class MakeTimestamptzTest extends DateMakingTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
            'DATE_PART' => DatePart::class,
            'MAKE_TIMESTAMPTZ' => MakeTimestamptz::class,
        ];
    }

    #[Test]
    public function can_create_timestamptz_from_components(): void
    {
        $dql = "SELECT MAKE_TIMESTAMPTZ(
                    CAST(DATE_PART('year', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('month', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('day', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('hour', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('minute', t.datetimetz1) AS INTEGER),
                    DATE_PART('second', t.datetimetz1)
                ) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-06-15 10:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_create_timestamptz_with_timezone(): void
    {
        $dql = "SELECT MAKE_TIMESTAMPTZ(
                    CAST(DATE_PART('year', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('month', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('day', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('hour', t.datetimetz1) AS INTEGER),
                    CAST(DATE_PART('minute', t.datetimetz1) AS INTEGER),
                    DATE_PART('second', t.datetimetz1),
                    'UTC'
                ) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-06-15 10:30:00+00', $result[0]['result']);
    }

    #[Test]
    public function can_create_timestamptz_from_entity_fields(): void
    {
        $dql = "SELECT MAKE_TIMESTAMPTZ(2023, n.integer1, n.integer2, 10, 30, 0, 'UTC') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics n
                WHERE n.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-10-20 10:30:00', $result[0]['result']);
    }
}
