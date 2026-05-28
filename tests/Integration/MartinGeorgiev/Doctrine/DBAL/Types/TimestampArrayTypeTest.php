<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::TIMESTAMP_ARRAY;
    }

    /**
     * @return array<string, array{array<int, \DateTimeImmutable|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single timestamp' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45'),
            ]],
            'timestamp with microseconds' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45.123456'),
            ]],
            'multiple timestamps' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45'),
                new \DateTimeImmutable('2024-01-01 00:00:00'),
            ]],
            'timestamp with null item' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45'),
                null,
                new \DateTimeImmutable('2024-01-01 00:00:00'),
            ]],
            'empty timestamp array' => [[]],
        ];
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_non_datetime_item(mixed $value): void
    {
        $this->expectException(InvalidTimestampArrayItemForDatabaseException::class);

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
            'string value' => ['2023-06-15 10:30:45'],
            'integer value' => [20230615],
        ];
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
                    $expectedItem->format('Y-m-d H:i:s.u'),
                    $actualItem->format('Y-m-d H:i:s.u'),
                    \sprintf('Timestamp mismatch at index %d for type %s', $index, $typeName)
                );
            }
        }
    }
}
