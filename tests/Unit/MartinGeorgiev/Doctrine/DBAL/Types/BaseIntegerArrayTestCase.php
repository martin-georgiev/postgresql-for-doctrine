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
     * @return list<mixed>
     */
    public static function provideInvalidDatabaseValueInputs(): array
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
        $this->assertEquals($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
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
}
