<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTime;
use PHPUnit\Framework\Attributes\Test;

class MakeTimeTest extends DateMakingTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CAST' => Cast::class,
            'DATE_PART' => DatePart::class,
            'MAKE_TIME' => MakeTime::class,
        ];
    }

    #[Test]
    public function can_create_time_from_components(): void
    {
        $dql = "SELECT MAKE_TIME(
                    CAST(DATE_PART('hour', t.datetime1) AS INTEGER),
                    CAST(DATE_PART('minute', t.datetime1) AS INTEGER),
                    DATE_PART('second', t.datetime1)
                ) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('10:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_create_time_from_entity_fields(): void
    {
        $dql = 'SELECT MAKE_TIME(n.integer1, n.integer2, 0) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('10:20:00', $result[0]['result']);
    }
}
