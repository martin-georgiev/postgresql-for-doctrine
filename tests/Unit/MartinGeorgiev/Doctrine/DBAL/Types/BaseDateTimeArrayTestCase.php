<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseDateTimeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseDateTimeArrayTestCase extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

    protected BaseDateTimeArray $fixture;

    abstract protected function createFixture(): BaseDateTimeArray;

    abstract protected function getExpectedTypeName(): string;

    /**
     * @return class-string<\Throwable>
     */
    abstract protected static function getPHPExceptionClass(): string;

    /**
     * @return class-string<\Throwable>
     */
    abstract protected static function getDatabaseExceptionClass(): string;

    /**
     * The format the era and expanded-year assertions render parsed values with. It uses X so that
     * years outside the four-digit range stay distinguishable from the years inside it.
     */
    abstract protected static function getComparisonFormat(): string;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = $this->createFixture();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame($this->getExpectedTypeName(), $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function converts_empty_array_to_database_value(): void
    {
        $this->assertSame('{}', $this->fixture->convertToDatabaseValue([], $this->platform));
    }

    #[Test]
    public function converts_empty_array_to_php_value(): void
    {
        $this->assertSame([], $this->fixture->convertToPHPValue('{}', $this->platform));
    }

    #[Test]
    public function validates_null_as_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(null));
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function validates_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'string' => ['any-string'],
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function validates_infinity_as_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(DateTimeInfinity::POSITIVE));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(DateTimeInfinity::NEGATIVE));
    }

    #[DataProvider('provideInfinityTransformations')]
    #[Test]
    public function converts_infinity_item_to_php_value(string $postgresValue, DateTimeInfinity $dateTimeInfinity): void
    {
        $this->assertSame($dateTimeInfinity, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    #[DataProvider('provideInfinityTransformations')]
    #[Test]
    public function converts_infinity_item_to_database_value(string $postgresValue, DateTimeInfinity $dateTimeInfinity): void
    {
        $this->assertSame(
            \sprintf('{"%s"}', $postgresValue),
            $this->fixture->convertToDatabaseValue([$dateTimeInfinity], $this->platform)
        );
    }

    /**
     * The PostgreSQL values are the ones a PostgreSQL 18 server emits inside a date[], timestamp[]
     * or timestamptz[]; it spells both of them the same way for all three types.
     *
     * @return array<string, array{postgresValue: string, dateTimeInfinity: DateTimeInfinity}>
     */
    public static function provideInfinityTransformations(): array
    {
        return [
            'positive infinity' => [
                'postgresValue' => 'infinity',
                'dateTimeInfinity' => DateTimeInfinity::POSITIVE,
            ],
            'negative infinity' => [
                'postgresValue' => '-infinity',
                'dateTimeInfinity' => DateTimeInfinity::NEGATIVE,
            ],
        ];
    }

    #[DataProvider('provideBcEraAndExpandedYearTransformationsToPHP')]
    #[Test]
    public function converts_bc_era_and_expanded_year_item_to_php_value(string $postgresValue, string $expectedFormattedValue): void
    {
        $result = $this->fixture->transformArrayItemForPHP($postgresValue);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedFormattedValue, $result->format(static::getComparisonFormat()));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedFormattedValue: string}>
     */
    abstract public static function provideBcEraAndExpandedYearTransformationsToPHP(): array;

    #[DataProvider('provideBcEraAndExpandedYearTransformationsToPostgres')]
    #[Test]
    public function converts_bc_era_and_expanded_year_item_to_database_value(\DateTimeImmutable $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeImmutable, expectedPostgresValue: string}>
     */
    abstract public static function provideBcEraAndExpandedYearTransformationsToPostgres(): array;

    /**
     * @throws \UnexpectedValueException when the format does not describe the given value
     */
    protected static function createImmutableDateTime(string $format, string $datetime): \DateTimeImmutable
    {
        $parsed = \DateTimeImmutable::createFromFormat($format, $datetime);
        if (!$parsed instanceof \DateTimeImmutable) {
            throw new \UnexpectedValueException(\sprintf('Cannot read "%s" as "%s"', $datetime, $format));
        }

        return $parsed;
    }

    #[Test]
    public function throws_exception_for_non_array_input_to_database(): void
    {
        $this->expectException(static::getPHPExceptionClass());
        $this->fixture->convertToDatabaseValue('not-an-array', $this->platform); // @phpstan-ignore-line
    }

    #[DataProvider('provideInvalidItemsForDatabase')]
    #[Test]
    public function throws_exception_for_invalid_item_in_database_array(mixed $item): void
    {
        $this->expectException(static::getDatabaseExceptionClass());
        $this->fixture->convertToDatabaseValue([$item], $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItemsForDatabase(): array
    {
        return [
            'string' => ['any-string'],
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidTypeInputsForPHP')]
    #[Test]
    public function throws_exception_for_invalid_type_input_for_php(mixed $value): void
    {
        $this->expectException(static::getPHPExceptionClass());
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
        $this->expectException(static::getPHPExceptionClass());
        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatInputsForPHP(): array
    {
        return [
            'garbage string' => ['not-a-datetime'],
            'empty string' => [''],
            'era suffix without a value' => [' BC'],
        ];
    }

    /**
     * Creates a mutable DateTime instance. Uses variable class instantiation
     * to prevent the date_time_immutable cs-fixer rule from rewriting it.
     */
    protected static function createMutableDateTime(string $datetime): \DateTimeInterface
    {
        /** @var class-string<\DateTimeInterface> $class */
        $class = 'DateTime';

        return new $class($datetime);
    }
}
