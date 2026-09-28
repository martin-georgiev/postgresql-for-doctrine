<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
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

    /**
     * @return array<string, array{phpValue: array<int, float|int|null>|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return \array_merge(parent::provideValidTransformations(), [
            'plain values' => [
                'phpValue' => [1.5, 2.5],
                'postgresValue' => '{1.5,2.5}',
            ],
            'null between two values' => [
                'phpValue' => [1.5, null, 3.5],
                'postgresValue' => '{1.5,NULL,3.5}',
            ],
            'null as the first element' => [
                'phpValue' => [null, 2.5],
                'postgresValue' => '{NULL,2.5}',
            ],
            'null as the last element' => [
                'phpValue' => [1.5, null],
                'postgresValue' => '{1.5,NULL}',
            ],
        ]);
    }

    #[Test]
    public function throws_exception_for_invalid_array_item_value(): void
    {
        $this->expectException(InvalidFloatArrayItemForPHPException::class);
        $this->expectExceptionMessage('cannot be transformed to valid PHP float');

        $this->fixture->transformArrayItemForPHP('1.e234');
    }

    /**
     * PostgreSQL stores and emits the non-finite values, so their spellings are valid items as strings too.
     *
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return \array_merge(parent::provideValidArrayItemsForDatabase(), [
            'infinity spelled as a string' => ['Infinity'],
            'not a number spelled as a string' => ['nan'],
        ]);
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function converts_item_for_postgres(float|int|string $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->createStub(AbstractPlatform::class)));
    }

    /**
     * An integer or a numeric string is written as given, and PostgreSQL reads it back as a float.
     *
     * @return array<string, array{phpValue: float|int|string, expectedPostgresValue: string}>
     */
    public static function provideValidItemTransformationsToPostgres(): array
    {
        return [
            'integer' => ['phpValue' => 2, 'expectedPostgresValue' => '{2}'],
            'numeric string' => ['phpValue' => '1.5', 'expectedPostgresValue' => '{1.5}'],
        ];
    }

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
