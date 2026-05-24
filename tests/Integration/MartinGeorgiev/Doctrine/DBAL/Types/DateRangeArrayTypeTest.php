<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;

class DateRangeArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'daterange[]';
    }

    /**
     * @return array<string, array{array<DateRange>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single daterange' => [[new DateRange(
                new \DateTimeImmutable('2023-01-01'),
                new \DateTimeImmutable('2023-12-31'),
                true,
                false
            )]],
            'multiple dateranges' => [[
                new DateRange(
                    new \DateTimeImmutable('2023-01-01'),
                    new \DateTimeImmutable('2023-06-30'),
                    true,
                    false
                ),
                new DateRange(
                    new \DateTimeImmutable('2023-07-01'),
                    new \DateTimeImmutable('2023-12-31'),
                    true,
                    false
                ),
            ]],
            'daterange via factory methods' => [[
                DateRange::year(2022),
                DateRange::month(2023, 3),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array type %s round-trip count mismatch', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $actualItem = $actual[$index];
            $this->assertInstanceOf(RangeValueObject::class, $expectedItem);
            $this->assertInstanceOf(RangeValueObject::class, $actualItem);
            $this->assertEquals(
                $expectedItem->__toString(),
                $actualItem->__toString(),
                \sprintf('Range string representation mismatch at index %d for type %s', $index, $typeName)
            );
        }
    }
}
