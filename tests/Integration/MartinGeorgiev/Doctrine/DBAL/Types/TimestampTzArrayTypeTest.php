<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class TimestampTzArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::TIMESTAMPTZ_ARRAY;
    }

    protected function getPostgresTypeName(): string
    {
        return 'TIMESTAMPTZ[]';
    }

    /**
     * @return array<string, array{string, array<int, \DateTimeImmutable|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single UTC timestamp' => ['single UTC timestamp', [
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
            ]],
            'positive timezone offset' => ['positive timezone offset', [
                new \DateTimeImmutable('2023-06-15 10:30:45+02:00'),
            ]],
            'negative timezone offset' => ['negative timezone offset', [
                new \DateTimeImmutable('2023-06-15 10:30:45-05:00'),
            ]],
            'multiple timestamptz values' => ['multiple timestamptz values', [
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                new \DateTimeImmutable('2024-01-01 00:00:00+02:00'),
            ]],
            'timestamptz with null item' => ['timestamptz with null item', [
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                null,
                new \DateTimeImmutable('2024-01-01 00:00:00-05:00'),
            ]],
            'empty timestamptz array' => ['empty timestamptz array', []],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_datetime_instance(): void
    {
        $this->expectException(InvalidTimestampTzArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['2023-06-15 10:30:45+00:00']);
    }

    #[Test]
    public function rejects_integer_instead_of_datetime_instance(): void
    {
        $this->expectException(InvalidTimestampTzArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), [20230615]);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('TimestampTzArray count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            if ($expectedItem === null) {
                $this->assertNull($actual[$index]);
            } else {
                $this->assertInstanceOf(\DateTimeImmutable::class, $actual[$index]);
                \assert($actual[$index] instanceof \DateTimeImmutable);
                $this->assertSame(
                    $expectedItem->getTimestamp(),
                    $actual[$index]->getTimestamp(),
                    \sprintf('TimestampTz mismatch at index %d for type %s', $index, $typeName)
                );
            }
        }
    }
}
