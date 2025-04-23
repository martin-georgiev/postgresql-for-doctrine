<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseIntegerArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;
use PHPUnit\Framework\TestCase;

abstract class BaseIntegerArrayTestCase extends TestCase
{
    protected BaseIntegerArray $fixture;

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
            ['1.23'],
            ['not_a_number'],
            ['1e2'],
            ['0xFF'],
            ['123abc'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(int $phpValue, string $postgresValue): void
    {
        self::assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(int $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return list<array{
     *     phpValue: int,
     *     postgresValue: string
     * }>
     */
    abstract public static function provideValidTransformations(): array;

    /**
     * @test
     *
     * @dataProvider provideOutOfRangeValues
     */
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
}
