<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseDateTimeArray;
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
    abstract protected static function getPhpExceptionClass(): string;

    /**
     * @return class-string<\Throwable>
     */
    abstract protected static function getDatabaseExceptionClass(): string;

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

    #[Test]
    public function can_validate_null_as_valid_array_item_for_database(): void
    {
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
            'string' => ['any-string'],
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

    #[Test]
    public function throws_exception_for_non_array_input_to_database(): void
    {
        $this->expectException(static::getPhpExceptionClass());
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
        $this->expectException(static::getPhpExceptionClass());
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
        $this->expectException(static::getPhpExceptionClass());
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
        ];
    }
}
