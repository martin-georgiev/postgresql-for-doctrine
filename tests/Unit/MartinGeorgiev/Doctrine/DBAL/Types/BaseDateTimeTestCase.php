<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseDateTime;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseDateTimeTestCase extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

    protected BaseDateTime $fixture;

    abstract protected function createFixture(): BaseDateTime;

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
     * The format the era and expanded-year assertions render parsed values with.
     * It uses X so that years outside the four-digit range stay distinguishable from the years inside it.
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

    #[DataProvider('provideInfinityTransformations')]
    #[Test]
    public function converts_infinity_to_php_value(string $postgresValue, DateTimeInfinity $dateTimeInfinity): void
    {
        $this->assertSame($dateTimeInfinity, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    #[DataProvider('provideInfinityTransformations')]
    #[Test]
    public function converts_infinity_to_database_value(string $postgresValue, DateTimeInfinity $dateTimeInfinity): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($dateTimeInfinity, $this->platform));
    }

    /**
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
    public function converts_bc_era_and_expanded_year_to_php_value(string $postgresValue, string $expectedFormattedValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedFormattedValue, $result->format(static::getComparisonFormat()));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedFormattedValue: string}>
     */
    abstract public static function provideBcEraAndExpandedYearTransformationsToPHP(): array;

    #[DataProvider('provideBcEraAndExpandedYearTransformationsToPostgres')]
    #[Test]
    public function converts_bc_era_and_expanded_year_to_database_value(\DateTimeImmutable $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeImmutable, expectedPostgresValue: string}>
     */
    abstract public static function provideBcEraAndExpandedYearTransformationsToPostgres(): array;

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(static::getDatabaseExceptionClass());

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string' => ['2023-06-15'],
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(static::getPHPExceptionClass());

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer' => [20230615],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueFormats')]
    #[Test]
    public function throws_exception_for_invalid_php_value_formats(string $value): void
    {
        $this->expectException(static::getPHPExceptionClass());

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueFormats(): array
    {
        return [
            'garbage string' => ['not-a-datetime'],
            'empty string' => [''],
            'era suffix without a value' => [' BC'],
            'infinity spelled as a float' => ['inf'],
        ];
    }

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
