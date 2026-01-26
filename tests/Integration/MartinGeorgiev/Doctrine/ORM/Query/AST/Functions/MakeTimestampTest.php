<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamp;
use PHPUnit\Framework\Attributes\Test;

class MakeTimestampTest extends DateMakingTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
            'DATE_PART' => DatePart::class,
            'MAKE_TIMESTAMP' => MakeTimestamp::class,
        ];
    }

    #[Test]
    public function can_create_timestamp_from_components(): void
    {
        $dql = "SELECT MAKE_TIMESTAMP(
                    CAST(DATE_PART('year', t.datetime1) AS INTEGER),
                    CAST(DATE_PART('month', t.datetime1) AS INTEGER),
                    CAST(DATE_PART('day', t.datetime1) AS INTEGER),
                    CAST(DATE_PART('hour', t.datetime1) AS INTEGER),
                    CAST(DATE_PART('minute', t.datetime1) AS INTEGER),
                    DATE_PART('second', t.datetime1)
                ) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('2023-06-15 10:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_create_timestamp_from_entity_fields(): void
    {
        $dql = 'SELECT MAKE_TIMESTAMP(2023, n.integer1, n.integer2, 10, 30, 0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('2023-10-20 10:30:00', $result[0]['result']);
    }
}
