<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class TimestampArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::TIMESTAMP_ARRAY;
    }

    protected function getPostgresTypeName(): string
    {
        return 'TIMESTAMP[]';
    }

    /**
     * @return array<string, array{string, array<int, \DateTimeImmutable|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single timestamp' => ['single timestamp', [
                new \DateTimeImmutable('2023-06-15 10:30:45'),
            ]],
            'timestamp with microseconds' => ['timestamp with microseconds', [
                new \DateTimeImmutable('2023-06-15 10:30:45.123456'),
            ]],
            'multiple timestamps' => ['multiple timestamps', [
                new \DateTimeImmutable('2023-06-15 10:30:45'),
                new \DateTimeImmutable('2024-01-01 00:00:00'),
            ]],
            'timestamp with null item' => ['timestamp with null item', [
                new \DateTimeImmutable('2023-06-15 10:30:45'),
                null,
                new \DateTimeImmutable('2024-01-01 00:00:00'),
            ]],
            'empty timestamp array' => ['empty timestamp array', []],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_datetime_instance(): void
    {
        $this->expectException(InvalidTimestampArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['2023-06-15 10:30:45']);
    }

    #[Test]
    public function rejects_integer_instead_of_datetime_instance(): void
    {
        $this->expectException(InvalidTimestampArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), [20230615]);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('TimestampArray count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            if ($expectedItem === null) {
                $this->assertNull($actual[$index]);
            } else {
                $this->assertInstanceOf(\DateTimeImmutable::class, $expectedItem);
                $actualItem = $actual[$index];
                $this->assertInstanceOf(\DateTimeImmutable::class, $actualItem);
                $this->assertSame(
                    $expectedItem->format('Y-m-d H:i:s'),
                    $actualItem->format('Y-m-d H:i:s'),
                    \sprintf('Timestamp mismatch at index %d for type %s', $index, $typeName)
                );
            }
        }
    }
}
