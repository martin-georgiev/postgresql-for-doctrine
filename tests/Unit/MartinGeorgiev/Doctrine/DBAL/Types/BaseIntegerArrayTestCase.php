<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseIntegerArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

abstract class BaseIntegerArrayTestCase extends TestCase
{
    protected BaseIntegerArray $fixture;

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function can_detect_invalid_for_transformation_php_value(mixed $phpValue): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'boolean' => [true],
            'null' => [null],
            'string' => ['string'],
            'array' => [[]],
            'object' => [new \stdClass()],
            'decimal' => ['1.23'],
            'not a number' => ['not_a_number'],
            'scientific notation' => ['1e2'],
            'hex notation' => ['0xFF'],
            'alphanumeric' => ['123abc'],
        ];
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(int $phpValue, string $postgresValue): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(int $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return list<array{
     *     phpValue: int,
     *     postgresValue: string
     * }>
     */
    abstract public static function provideValidTransformations(): array;

    #[DataProvider('provideOutOfRangeValues')]
    #[Test]
    public function throws_domain_exception_when_value_exceeds_range(string $outOfRangeValue): void
    {
        $this->expectException(InvalidIntegerArrayItemForPHPException::class);
        $this->expectExceptionMessage('is out of range for PostgreSQL');

        $this->fixture->transformArrayItemForPHP($outOfRangeValue);
    }

    /**
     * @return array<array{string}>
     */
    abstract public static function provideOutOfRangeValues(): array;

    #[Test]
    public function returns_null_when_transforming_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[DataProvider('provideInvalidTypeInputsForPHP')]
    #[Test]
    public function throws_exception_when_transforming_non_integer_type_for_php(mixed $value): void
    {
        $this->expectException(InvalidIntegerArrayItemForPHPException::class);

        $this->fixture->transformArrayItemForPHP($value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputsForPHP(): array
    {
        return [
            'boolean' => [true],
            'array' => [[]],
            'object' => [new \stdClass()],
            'string' => ['string'],
            'decimal' => ['1.23'],
            'not a number' => ['not_a_number'],
            'scientific notation' => ['1e2'],
            'hex notation' => ['0xFF'],
            'alphanumeric' => ['123abc'],
        ];
    }
}
