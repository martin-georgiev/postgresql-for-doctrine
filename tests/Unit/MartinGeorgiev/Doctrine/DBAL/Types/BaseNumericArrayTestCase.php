<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

abstract class BaseNumericArrayTestCase extends TestCase
{
    protected BaseArray $fixture;

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function can_detect_invalid_for_transformation_php_value(mixed $phpValue): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @return array<string, array{mixed}>
     */
    abstract public static function provideInvalidDatabaseValueInputs(): array;

    /**
     * @return array<string, array{mixed}>
     */
    protected static function commonInvalidDatabaseValueInputs(): array
    {
        return [
            'boolean' => [true],
            'null' => [null],
            'string' => ['string'],
            'array' => [[]],
            'object' => [new \stdClass()],
            'not a number' => ['not_a_number'],
        ];
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(float|int $phpValue, string $postgresValue): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(float|int $phpValue, string $postgresValue): void
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
    public function returns_null_when_transforming_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
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
