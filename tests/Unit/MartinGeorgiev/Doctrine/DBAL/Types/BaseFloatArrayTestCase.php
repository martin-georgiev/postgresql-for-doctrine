<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseFloatArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

abstract class BaseFloatArrayTestCase extends TestCase
{
    protected BaseFloatArray $fixture;

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
            'invalid scientific notation (trailing e)' => ['1e'],
            'invalid scientific notation (leading e)' => ['e1'],
            'invalid number format' => ['1.23.45'],
            'not a number' => ['not_a_number'],
        ];
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(float $phpValue, string $postgresValue): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(float $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return list<array{
     *     phpValue: float,
     *     postgresValue: string
     * }>
     */
    abstract public static function provideValidTransformations(): array;

    #[Test]
    public function throws_domain_exception_when_invalid_array_item_value(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('cannot be transformed to valid PHP float');

        $this->fixture->transformArrayItemForPHP('1.e234');
    }

    #[Test]
    public function returns_null_when_transforming_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[DataProvider('provideInvalidTypeInputsForPHP')]
    #[Test]
    public function throws_exception_when_transforming_non_numeric_type_for_php(mixed $value): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);

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
            'invalid scientific notation (trailing e)' => ['1e'],
            'invalid scientific notation (leading e)' => ['e1'],
            'invalid number format' => ['1.23.45'],
            'not a number' => ['not_a_number'],
        ];
    }
}
