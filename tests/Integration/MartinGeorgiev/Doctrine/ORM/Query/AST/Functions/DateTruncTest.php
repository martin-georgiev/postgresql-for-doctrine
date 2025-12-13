<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DateTruncTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_TRUNC' => DateTrunc::class,
        ];
    }

    #[DataProvider('provideTruncFieldCases')]
    #[Test]
    public function can_truncate_to_field(string $field, string $expected): void
    {
        $dql = \sprintf(
            "SELECT DATE_TRUNC('%s', t.datetime1) as result
             FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
             WHERE t.id = 1",
            $field
        );
        $result = $this->executeDqlQuery($dql);
        $this->assertSame($expected, $result[0]['result']);
    }

    /**
     * @return \Generator<string, array{string, string}>
     */
    public static function provideTruncFieldCases(): \Generator
    {
        yield 'microseconds' => ['microseconds', '2023-06-15 10:30:00'];
        yield 'milliseconds' => ['milliseconds', '2023-06-15 10:30:00'];
        yield 'second' => ['second', '2023-06-15 10:30:00'];
        yield 'minute' => ['minute', '2023-06-15 10:30:00'];
        yield 'hour' => ['hour', '2023-06-15 10:00:00'];
        yield 'day' => ['day', '2023-06-15 00:00:00'];
        yield 'week' => ['week', '2023-06-12 00:00:00']; // Monday of that week
        yield 'month' => ['month', '2023-06-01 00:00:00'];
        yield 'quarter' => ['quarter', '2023-04-01 00:00:00'];
        yield 'year' => ['year', '2023-01-01 00:00:00'];
        yield 'decade' => ['decade', '2020-01-01 00:00:00'];
        yield 'century' => ['century', '2001-01-01 00:00:00'];
        yield 'millennium' => ['millennium', '2001-01-01 00:00:00'];
    }

    #[Test]
    public function can_truncate_timestamptz_with_timezone(): void
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
