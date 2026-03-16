<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class IntervalTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'interval';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INTERVAL';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$actual instanceof IntervalValueObject) {
            throw new \InvalidArgumentException('IntervalTypeTest expects actual value to be an Interval object');
        }

        if (!$expected instanceof IntervalValueObject && !\is_string($expected)) {
            throw new \InvalidArgumentException('IntervalTypeTest expects expected value to be an Interval object or string');
        }

        $this->assertSame((string) $expected, (string) $actual);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(IntervalValueObject $intervalValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $intervalValueObject);
    }

    /**
     * @return array<string, array{IntervalValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'one year' => [IntervalValueObject::fromString('1 year')],
            'time only' => [IntervalValueObject::fromString('04:05:06')],
        ];
    }

    #[DataProvider('provideNormalizedTransformations')]
    #[Test]
    public function can_handle_postgresql_normalization_on_storage(string $inputValue, string $expectedValue): void
    {
        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $this->getTypeName(),
            $this->getPostgresTypeName(),
            IntervalValueObject::fromString($inputValue),
            IntervalValueObject::fromString($expectedValue)
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideNormalizedTransformations(): array
    {
        return [
            'ISO 8601 gets normalized to verbose' => ['P1Y', '1 year'],
            'ISO 8601 full gets normalized' => ['P1Y2M3DT4H5M6S', '1 year 2 mons 3 days 04:05:06'],
            'verbose months gets abbreviated' => ['1 year 2 months 3 days', '1 year 2 mons 3 days'],
            'verbose time parts get compacted' => ['1 year 2 months 3 days 4 hours 5 minutes 6 seconds', '1 year 2 mons 3 days 04:05:06'],
        ];
    }
}
