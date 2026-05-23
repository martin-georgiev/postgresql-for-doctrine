<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntervalArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class IntervalArrayTypeTest extends ArrayTypeTestCase
{
    use IntervalAssertionTrait;

    protected function getTypeName(): string
    {
        return Type::INTERVAL_ARRAY;
    }

    /**
     * @return array<string, array{array<int, IntervalValueObject|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single year interval' => [[
                IntervalValueObject::fromString('1 year'),
            ]],
            'time-only interval' => [[
                IntervalValueObject::fromString('04:05:06'),
            ]],
            'full interval' => [[
                IntervalValueObject::fromString('1 year 2 mons 3 days 04:05:06'),
            ]],
            'multiple intervals' => [[
                IntervalValueObject::fromString('1 year'),
                IntervalValueObject::fromString('2 mons'),
                IntervalValueObject::fromString('04:05:06'),
            ]],
            'negative interval' => [[
                IntervalValueObject::fromString('-04:05:06'),
            ]],
            'zero interval' => [[
                IntervalValueObject::fromString('00:00:00'),
            ]],
            'days only' => [[
                IntervalValueObject::fromString('30 days'),
            ]],
            'interval array with null item' => [[
                IntervalValueObject::fromString('1 year'),
                null,
                IntervalValueObject::fromString('04:05:06'),
            ]],
        ];
    }

    #[Test]
    public function can_handle_string_items(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['1 year', '1 hour']);
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_non_interval_item(mixed $value): void
    {
        $this->expectException(InvalidIntervalArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, [$value]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItems(): array
    {
        return [
            'integer value' => [42],
            'float value' => [3.14],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!\is_array($expected) || !\is_array($actual)) {
            parent::assertTypeValueEquals($expected, $actual, $typeName);

            return;
        }

        $this->assertCount(\count($expected), $actual, \sprintf('Interval array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            if ($expectedItem === null) {
                $this->assertNull($actual[$index]);

                continue;
            }

            if (\is_string($expectedItem)) {
                $this->assertIntervalEquals($expectedItem, $actual[$index], $typeName);

                continue;
            }

            $this->assertInstanceOf(IntervalValueObject::class, $expectedItem);
            $this->assertIntervalEquals($expectedItem, $actual[$index], $typeName);
        }
    }
}
