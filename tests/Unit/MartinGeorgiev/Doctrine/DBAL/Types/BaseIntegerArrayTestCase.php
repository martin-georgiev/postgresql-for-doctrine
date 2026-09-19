<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class BaseIntegerArrayTestCase extends BaseNumericArrayTestCase
{
    protected static function getInvalidItemException(): string
    {
        return InvalidIntegerArrayItemForPHPException::class;
    }

    protected static function getInvalidDatabaseItemException(): string
    {
        return InvalidIntegerArrayItemForDatabaseException::class;
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(static::commonInvalidDatabaseValueInputs(), [
            'decimal' => ['1.23'],
            'scientific notation' => ['1e2'],
            'hex notation' => ['0xFF'],
            'alphanumeric' => ['123abc'],
        ]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputsForPHP(): array
    {
        return \array_merge(static::commonInvalidTypeInputsForPHP(), [
            'decimal' => ['1.23'],
            'scientific notation' => ['1e2'],
            'hex notation' => ['0xFF'],
            'alphanumeric' => ['123abc'],
        ]);
    }

    /**
     * @return array<string, array{phpValue: array<int, float|int|null>, postgresValue: string}>
     */
    public static function provideValidArrayTransformations(): array
    {
        return \array_merge(parent::provideValidArrayTransformations(), [
            'plain values' => [
                'phpValue' => [1, 2, 3],
                'postgresValue' => '{1,2,3}',
            ],
            'null between two values' => [
                'phpValue' => [1, null, 3],
                'postgresValue' => '{1,NULL,3}',
            ],
            'null as the first element' => [
                'phpValue' => [null, 2],
                'postgresValue' => '{NULL,2}',
            ],
            'null as the last element' => [
                'phpValue' => [1, null],
                'postgresValue' => '{1,NULL}',
            ],
        ]);
    }

    #[DataProvider('provideOutOfRangeValues')]
    #[Test]
    public function throws_exception_for_value_exceeding_range(string $outOfRangeValue): void
    {
        $this->expectException(InvalidIntegerArrayItemForPHPException::class);
        $this->expectExceptionMessage('is out of range for PostgreSQL');

        $this->fixture->transformArrayItemForPHP($outOfRangeValue);
    }

    /**
     * @return array<string, array{string}>
     */
    abstract public static function provideOutOfRangeValues(): array;
}
