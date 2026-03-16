<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TimestampTzArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TimestampTzArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TimestampTzArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TimestampTzArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('timestamptz[]', $this->fixture->getName());
    }

    #[Test]
    public function can_convert_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function can_convert_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function can_convert_empty_array_to_database_value(): void
    {
        $this->assertSame('{}', $this->fixture->convertToDatabaseValue([], $this->platform));
    }

    #[Test]
    public function can_convert_empty_postgres_array_to_php_value(): void
    {
        $this->assertSame([], $this->fixture->convertToPHPValue('{}', $this->platform));
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function can_transform_timestamptz_item_for_postgres(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeInterface, expectedPostgresValue: string}>
     */
    public static function provideValidItemTransformationsToPostgres(): array
    {
        return [
            'UTC offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                'expectedPostgresValue' => '{2023-06-15 10:30:45+00:00}',
            ],
            'positive offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45+02:00'),
                'expectedPostgresValue' => '{2023-06-15 10:30:45+02:00}',
            ],
            'negative offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45-05:00'),
                'expectedPostgresValue' => '{2023-06-15 10:30:45-05:00}',
            ],
        ];
    }

    #[DataProvider('provideValidItemTransformationsToPHP')]
    #[Test]
    public function can_transform_timestamptz_item_for_php(string $postgresValue, string $expectedDatetime, string $expectedOffset): void
    {
        $result = $this->fixture->transformArrayItemForPHP($postgresValue);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedDatetime, $result->format('Y-m-d H:i:s'));
        $this->assertSame($expectedOffset, $result->format('P'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedDatetime: string, expectedOffset: string}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'UTC offset' => [
                'postgresValue' => '2023-06-15 10:30:45+00:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '+00:00',
            ],
            'positive offset' => [
                'postgresValue' => '2023-06-15 10:30:45+02:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '+02:00',
            ],
            'negative offset' => [
                'postgresValue' => '2023-06-15 10:30:45-05:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '-05:00',
            ],
        ];
    }

    #[Test]
    public function can_convert_multiple_timestamptz_values_to_database(): void
    {
        $phpValue = [
            new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
            new \DateTimeImmutable('2024-01-01 00:00:00+02:00'),
        ];
        $result = $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
        $this->assertSame('{2023-06-15 10:30:45+00:00,2024-01-01 00:00:00+02:00}', $result);
    }

    #[Test]
    public function can_convert_multiple_timestamptz_values_from_database(): void
    {
        $result = $this->fixture->convertToPHPValue('{2023-06-15 10:30:45+00:00,2024-01-01 00:00:00+02:00}', $this->platform);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[0]);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[1]);
        $this->assertSame('2023-06-15 10:30:45', $result[0]->format('Y-m-d H:i:s'));
        $this->assertSame('+00:00', $result[0]->format('P'));
        $this->assertSame('2024-01-01 00:00:00', $result[1]->format('Y-m-d H:i:s'));
        $this->assertSame('+02:00', $result[1]->format('P'));
    }

    #[Test]
    public function can_validate_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45+00:00')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTime('2023-06-15 10:30:45+02:00')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(null));
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function can_detect_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'string' => ['2023-06-15 10:30:45+00:00'],
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function throws_exception_for_non_array_input_to_database(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fixture->convertToDatabaseValue('not-an-array', $this->platform); // @phpstan-ignore-line
    }

    #[DataProvider('provideInvalidItemsForDatabase')]
    #[Test]
    public function throws_exception_for_invalid_item_in_database_array(mixed $item): void
    {
        $this->expectException(InvalidTimestampTzArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue([$item], $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItemsForDatabase(): array
    {
        return [
            'string timestamp' => ['2023-06-15 10:30:45+00:00'],
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function can_return_null_for_null_item_in_transform_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[DataProvider('provideInvalidTypeInputsForPHP')]
    #[Test]
    public function throws_exception_for_invalid_type_input_for_php(mixed $value): void
    {
        $this->expectException(InvalidTimestampTzArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputsForPHP(): array
    {
        return [
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidFormatInputsForPHP')]
    #[Test]
    public function throws_exception_for_invalid_format_input_for_php(string $value): void
    {
        $this->expectException(InvalidTimestampTzArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatInputsForPHP(): array
    {
        return [
            'garbage string' => ['not-a-timestamp'],
            'date only' => ['2023-06-15'],
            'timestamp without timezone' => ['2023-06-15 10:30:45'],
            'empty string' => [''],
        ];
    }
}
