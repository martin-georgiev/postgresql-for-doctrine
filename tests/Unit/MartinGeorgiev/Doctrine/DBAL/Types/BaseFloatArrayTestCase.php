<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseFloatArray;
use PHPUnit\Framework\TestCase;

abstract class BaseFloatArrayTestCase extends TestCase
{
    protected BaseFloatArray $fixture;

    /**
     * @test
     *
     * @dataProvider provideInvalidPHPValuesForDatabaseTransformation
     */
    public function can_detect_invalid_for_transformation_php_value(mixed $phpValue): void
    {
        self::assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @return list<mixed>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
            [true],
            [null],
            ['string'],
            [[]],
            [new \stdClass()],
            ['1e'], // Invalid scientific notation format
            ['e1'], // Invalid scientific notation format
            ['1.23.45'], // Invalid number format
            ['not_a_number'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(float $phpValue, string $postgresValue): void
    {
        self::assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(float $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return list<array{
     *     phpValue: float,
     *     postgresValue: string
     * }>
     */
    abstract public static function provideValidTransformations(): array;

    /**
     * @test
     */
    public function throws_domain_exception_when_invalid_array_item_value(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('cannot be transformed to valid PHP float');

        $this->fixture->transformArrayItemForPHP('1.e234');
    }
}
