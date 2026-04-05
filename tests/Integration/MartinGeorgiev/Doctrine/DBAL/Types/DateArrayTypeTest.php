<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class DateArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::DATE_ARRAY;
    }

    protected function getPostgresTypeName(): string
    {
        return 'DATE[]';
    }

    /**
     * @return array<string, array{array<int, \DateTimeImmutable|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single date' => [[
                new \DateTimeImmutable('2023-06-15'),
            ]],
            'multiple dates' => [[
                new \DateTimeImmutable('2023-06-15'),
                new \DateTimeImmutable('2024-02-29'),
                new \DateTimeImmutable('2000-01-01'),
            ]],
            'date with null item' => [[
                new \DateTimeImmutable('2023-06-15'),
                null,
                new \DateTimeImmutable('2024-02-29'),
            ]],
            'empty date array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_datetime_instance(): void
    {
        $this->expectException(InvalidDateArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['2023-06-15']);
    }

    #[Test]
    public function rejects_integer_instead_of_datetime_instance(): void
    {
        $this->expectException(InvalidDateArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), [20230615]);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('DateArray count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            if ($expectedItem === null) {
                $this->assertNull($actual[$index]);
            } else {
                $this->assertInstanceOf(\DateTimeImmutable::class, $expectedItem);
                $actualItem = $actual[$index];
                $this->assertInstanceOf(\DateTimeImmutable::class, $actualItem);
                $this->assertSame(
                    $expectedItem->format('Y-m-d'),
                    $actualItem->format('Y-m-d'),
                    \sprintf('Date mismatch at index %d for type %s', $index, $typeName)
                );
            }
        }
    }
}
