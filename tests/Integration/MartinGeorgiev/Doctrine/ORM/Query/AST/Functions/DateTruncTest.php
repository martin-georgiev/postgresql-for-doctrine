<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc;
use PHPUnit\Framework\Attributes\Test;

final class DateTruncTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_TRUNC' => DateTrunc::class,
        ];
    }

    #[Test]
    public function returns_the_truncated_timestamp_from_an_entity_field(): void
    {
        $dql = "SELECT DATE_TRUNC('day', t.datetime1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023-06-15 00:00:00', $result[0]['result']);
    }

    #[Test]
    public function returns_the_truncated_timestamp_from_an_entity_field_in_a_time_zone(): void
    {
        $dql = "SELECT DATE_TRUNC('day', t.datetimetz1, 'Australia/Adelaide') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        // The input is '2023-06-15 10:30:00+00' (UTC)
        // In Australia/Adelaide (UTC+9:30), this is '2023-06-15 20:00:00'
        // Truncated to day in Adelaide timezone gives '2023-06-15 00:00:00+09:30'
        // Converted back to UTC: '2023-06-14 14:30:00+00'
        $this->assertSame('2023-06-14 14:30:00+00', $result[0]['result']);
    }
}
