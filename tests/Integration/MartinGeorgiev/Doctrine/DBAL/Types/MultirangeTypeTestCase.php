<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Multirange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class MultirangeTypeTestCase extends TestCase
{
    /**
     * @param Multirange<Range<\DateTimeInterface|float|int>> $multirange
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(Multirange $multirange): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $multirange);
    }

    /**
     * @return array<string, array{Multirange<Range<\DateTimeInterface|float|int>>}>
     */
    abstract public static function provideValidTransformations(): array;

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof Multirange || !$actual instanceof Multirange) {
            throw new \InvalidArgumentException('assertTypeValueEquals in MultirangeTypeTestCase expects Multirange arguments.');
        }

        $this->assertSame(
            (string) $expected,
            (string) $actual,
            \sprintf('Multirange string representation mismatch for type %s', $typeName)
        );
    }
}
