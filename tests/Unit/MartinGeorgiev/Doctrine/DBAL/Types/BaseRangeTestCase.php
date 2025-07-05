<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseRangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseRangeTestCase extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

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

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?Range $range, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($range, $this->platform));
    }

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

    /**
     * Each array contains [phpValue, postgresValue] pairs.
     */
    abstract public static function provideValidTransformations(): \Generator;

    abstract protected function createRangeType(): BaseRangeType;

    /**
     * Returns the expected type name (e.g., 'numrange', 'int4range').
     */
    abstract protected function getExpectedTypeName(): string;

    /**
     * Returns the expected SQL declaration (e.g., 'NUMRANGE', 'INT4RANGE').
     */
    abstract protected function getExpectedSqlDeclaration(): string;

    abstract protected function getExpectedValueObjectClass(): string;
}
