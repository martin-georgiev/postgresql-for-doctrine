<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TstzRangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tstzrange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSTZRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, TstzRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple tstzrange' => ['simple tstzrange', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
                false,
                false
            )],
            'tstzrange with inclusive bounds' => ['tstzrange with inclusive bounds', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 09:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 17:00:00+00:00'),
                true,
                true
            )],
            'tstzrange with different timezones' => ['tstzrange with different timezones', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00+02:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+02:00'),
                false,
                false
            )],
            'tstzrange with UTC' => ['tstzrange with UTC', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-06-15 08:00:00+00:00'),
                new \DateTimeImmutable('2023-06-15 16:00:00+00:00'),
                false,
                false
            )],
        ];
    }

    #[Test]
    public function can_handle_infinite_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $tstzRange = new TstzRangeValueObject(null, null, false, false);
        $this->runTypeTest($typeName, $columnType, $tstzRange);
    }

    #[Test]
    public function can_handle_empty_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        // lower > upper shall result in an empty range
        $tstzRange = new TstzRangeValueObject(
            new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
            false,
            false
        );
        $this->runTypeTest($typeName, $columnType, $tstzRange);
    }
}
