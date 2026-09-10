<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidFloatArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class BaseFloatArrayTestCase extends BaseNumericArrayTestCase
{
    protected static function getInvalidItemException(): string
    {
        return InvalidFloatArrayItemForPHPException::class;
    }

    protected static function getInvalidDatabaseItemException(): string
    {
        return InvalidFloatArrayItemForDatabaseException::class;
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(static::commonInvalidDatabaseValueInputs(), [
            'invalid scientific notation (trailing e)' => ['1e'],
            'invalid scientific notation (leading e)' => ['e1'],
            'invalid number format' => ['1.23.45'],
        ]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputsForPHP(): array
    {
        return \array_merge(static::commonInvalidTypeInputsForPHP(), [
            'invalid scientific notation (trailing e)' => ['1e'],
            'invalid scientific notation (leading e)' => ['e1'],
            'invalid number format' => ['1.23.45'],
        ]);
    }

    #[Test]
    public function throws_exception_for_invalid_array_item_value(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('cannot be transformed to valid PHP float');

        $this->fixture->transformArrayItemForPHP('1.e234');
    }

    #[DataProvider('providePostgresOutputValues')]
    #[Test]
    public function converts_postgres_output_to_php_value(string $postgresValue, float $phpValue): void
    {
        $this->assertSame($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return array<string, array{postgresValue: string, phpValue: float}>
     */
    abstract public static function providePostgresOutputValues(): array;

    #[DataProvider('provideValidScientificNotationStrings')]
    #[Test]
    public function validates_scientific_notation_string_for_database(string $item): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($item));
    }

    /**
     * @return array<string, array{string}>
     */
    abstract public static function provideValidScientificNotationStrings(): array;
}
