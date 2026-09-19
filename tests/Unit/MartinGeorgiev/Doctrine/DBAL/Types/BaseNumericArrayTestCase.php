<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

abstract class BaseNumericArrayTestCase extends TestCase
{
    protected BaseArray $fixture;

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function detects_invalid_for_transformation_php_value(mixed $phpValue): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(static::getInvalidDatabaseItemException());

        $this->fixture->convertToDatabaseValue([$phpValue], $this->createStub(AbstractPlatform::class));
    }

    /**
     * @return array<string, array{mixed}>
     */
    abstract public static function provideInvalidDatabaseValueInputs(): array;

    /**
     * @return class-string<\Throwable>
     */
    abstract protected static function getInvalidDatabaseItemException(): string;

    /**
     * @return array<string, array{mixed}>
     */
    protected static function commonInvalidDatabaseValueInputs(): array
    {
        return [
            'boolean' => [true],
            'string' => ['string'],
            'array' => [[]],
            'object' => [new \stdClass()],
            'not a number' => ['not_a_number'],
        ];
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(float|int $phpValue, string $postgresValue): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(float|int $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return list<array{
     *     phpValue: float|int,
     *     postgresValue: string
     * }>
     */
    abstract public static function provideValidTransformations(): array;

    #[Test]
    public function converts_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function converts_null_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(null));
    }

    /**
     * @param array<int, float|int|null> $phpValue
     */
    #[DataProvider('provideValidArrayTransformations')]
    #[Test]
    public function converts_array_to_database_value(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->createStub(AbstractPlatform::class)));
    }

    /**
     * @param array<int, float|int|null> $phpValue
     */
    #[DataProvider('provideValidArrayTransformations')]
    #[Test]
    public function converts_array_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->createStub(AbstractPlatform::class)));
    }

    /**
     * An empty array and an array of nothing but nulls read back the same whatever the element type is.
     *
     * @return array<string, array{phpValue: array<int, float|int|null>, postgresValue: string}>
     */
    public static function provideValidArrayTransformations(): array
    {
        return [
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'a single null' => [
                'phpValue' => [null],
                'postgresValue' => '{NULL}',
            ],
            'nothing but nulls' => [
                'phpValue' => [null, null],
                'postgresValue' => '{NULL,NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('is not in a valid format');

        $this->fixture->convertToPHPValue($postgresValue, $this->createStub(AbstractPlatform::class));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'no literal at all' => [''],
            'empty element' => ['{1,,3}'],
            'multi-dimensional array' => ['{{1},{2}}'],
            'missing closing brace' => ['{1,2'],
            'missing opening brace' => ['1,2}'],
            'no braces at all' => ['1,2'],
        ];
    }

    /**
     * @return class-string<\Throwable>
     */
    abstract protected static function getInvalidItemException(): string;

    #[DataProvider('provideInvalidTypeInputsForPHP')]
    #[Test]
    public function throws_exception_when_transforming_invalid_type_for_php(mixed $value): void
    {
        $this->expectException(static::getInvalidItemException());

        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    abstract public static function provideInvalidTypeInputsForPHP(): array;

    /**
     * @return array<string, array{mixed}>
     */
    protected static function commonInvalidTypeInputsForPHP(): array
    {
        return [
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
            'string' => ['string'],
            'not a number' => ['not_a_number'],
        ];
    }
}
