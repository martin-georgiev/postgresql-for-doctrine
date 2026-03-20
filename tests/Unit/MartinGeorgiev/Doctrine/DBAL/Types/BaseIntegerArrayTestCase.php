<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class BaseIntegerArrayTestCase extends BaseNumericArrayTestCase
{
    protected static function getInvalidItemException(): string
    {
        return InvalidIntegerArrayItemForPHPException::class;
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

    #[DataProvider('provideOutOfRangeValues')]
    #[Test]
    public function throws_domain_exception_when_value_exceeds_range(string $outOfRangeValue): void
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
