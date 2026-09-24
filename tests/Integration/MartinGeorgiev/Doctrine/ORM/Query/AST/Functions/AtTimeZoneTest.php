<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone;
use PHPUnit\Framework\Attributes\Test;

final class AtTimeZoneTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'AT_TIME_ZONE' => AtTimeZone::class,
        ];
    }

    #[Test]
    public function converts_the_timestamp_to_another_time_zone_from_an_entity_field(): void
    {
        $dql = "SELECT AT_TIME_ZONE(t.datetimetz1, 'Europe/Athens') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023-06-15 13:30:00', $result[0]['result']);
    }
}
