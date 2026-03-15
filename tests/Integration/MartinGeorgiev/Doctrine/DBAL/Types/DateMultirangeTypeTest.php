<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange as DateMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DateMultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'datemultirange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'DATEMULTIRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_multirange_value(DateMultirangeVO $dateMultirangeVO): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $dateMultirangeVO);
    }

    /**
     * @return array<string, array{DateMultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new DateMultirangeVO([])],
            'single date range' => [
                new DateMultirangeVO([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'))]),
            ],
            'two non-overlapping date ranges' => [
                new DateMultirangeVO([
                    new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31')),
                    new DateRange(new \DateTimeImmutable('2024-07-01'), new \DateTimeImmutable('2024-12-31')),
                ]),
            ],
        ];
    }

    #[Test]
    public function can_normalizes_exclusive_lower_bound_to_inclusive(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $input = new DateMultirangeVO([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'), false, false)]);
        $expected = new DateMultirangeVO([new DateRange(new \DateTimeImmutable('2024-01-02'), new \DateTimeImmutable('2024-06-30'))]);

        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $input, $typeName)
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($typeName, $tableName, $columnName);
            $this->assertInstanceOf(DateMultirangeVO::class, $retrieved);
            $this->assertSame((string) $expected, (string) $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
