<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseRangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @template R of Range
 */
abstract class BaseRangeTestCase extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

    /**
     * @var BaseRangeType<R>
     */
    protected BaseRangeType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = $this->createRangeType();
    }

    #[Test]
    public function has_name(): void
    {
        self::assertEquals($this->getExpectedTypeName(), $this->fixture->getName());
    }

    /**
     * @param R|null $range
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?Range $range, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($range, $this->platform));
    }

    /**
     * @param R|null $range
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?Range $range, ?string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        if (!$range instanceof Range) {
            self::assertNull($result);
        } else {
            self::assertInstanceOf($this->getExpectedValueObjectClass(), $result);
            self::assertEquals($range->__toString(), $result->__toString());
            self::assertEquals($range->isEmpty(), $result->isEmpty());
        }
    }

    /**
     * Each array contains [phpValue, postgresValue] pairs.
     */
    abstract public static function provideValidTransformations(): \Generator;

    #[Test]
    public function can_transform_null_from_php_value(): void
    {
        $result = $this->fixture->convertToDatabaseValue(null, $this->platform);

        self::assertNull($result);
    }

    #[Test]
    public function can_transform_null_from_sql_value(): void
    {
        $result = $this->fixture->convertToPHPValue(null, $this->platform);

        self::assertNull($result);
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf($this->getExpectedValueObjectClass(), $result);
        self::assertEquals('empty', (string) $result);
        self::assertTrue($result->isEmpty());
    }

    #[Test]
    public function can_handle_empty_string_from_sql(): void
    {
        $result = $this->fixture->convertToPHPValue('', $this->platform);

        self::assertNull($result);
    }

    #[Test]
    public function throws_exception_for_invalid_php_value_type(): void
    {
        $this->expectException(InvalidRangeForDatabaseException::class);
        $this->expectExceptionMessage('Invalid type for range');

        $this->fixture->convertToDatabaseValue('invalid', $this->platform); // @phpstan-ignore-line argument.type
    }

    #[Test]
    public function throws_exception_for_invalid_sql_value_type(): void
    {
        $this->expectException(InvalidRangeForPHPException::class);
        $this->expectExceptionMessage('Invalid database value type for range conversion');

        $this->fixture->convertToPHPValue([1, 2], $this->platform); // @phpstan-ignore-line argument.type
    }

    #[Test]
    public function throws_exception_for_invalid_range_format(): void
    {
        $this->expectException(InvalidRangeForPHPException::class);
        $this->expectExceptionMessage('Invalid range format from database');

        $this->fixture->convertToPHPValue('{1,2}', $this->platform);
    }

    /**
     * @return BaseRangeType<R>
     */
    abstract protected function createRangeType(): BaseRangeType;

    /**
     * Returns the expected type name (e.g., 'numrange', 'int4range').
     */
    abstract protected function getExpectedTypeName(): string;

    /**
     * @return class-string<R>
     */
    abstract protected function getExpectedValueObjectClass(): string;
}
