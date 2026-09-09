<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fixtures\MartinGeorgiev\Doctrine\Colors;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteColorArrayType;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteTrickyLabelArrayType;
use Fixtures\MartinGeorgiev\Doctrine\Sizes;
use Fixtures\MartinGeorgiev\Doctrine\TrickyLabels;
use MartinGeorgiev\Doctrine\DBAL\Types\EnumArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidEnumArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class EnumArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private ConcreteColorArrayType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new ConcreteColorArrayType();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('test_color[]', $this->fixture->getName());
    }

    #[Test]
    public function returns_sql_declaration_as_type_name(): void
    {
        $this->assertSame('test_color[]', $this->fixture->getSQLDeclaration([], $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<Colors|null>|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single case' => [
                'phpValue' => [Colors::RED],
                'postgresValue' => '{"red"}',
            ],
            'multiple cases' => [
                'phpValue' => [Colors::RED, Colors::BLUE],
                'postgresValue' => '{"red","blue"}',
            ],
            'cases mixed with a null element' => [
                'phpValue' => [Colors::RED, null, Colors::BLUE],
                'postgresValue' => '{"red",NULL,"blue"}',
            ],
        ];
    }

    #[Test]
    public function converts_unquoted_items_to_php_value(): void
    {
        // PostgreSQL only quotes labels that need it, so an ordinary label arrives bare
        $result = $this->fixture->convertToPHPValue('{red,blue}', $this->platform);

        $this->assertSame([Colors::RED, Colors::BLUE], $result);
    }

    #[DataProvider('provideLabelsNeedingEscaping')]
    #[Test]
    public function converts_labels_needing_escaping_to_database_value(TrickyLabels $trickyLabels, string $postgresValue): void
    {
        $concreteTrickyLabelArrayType = new ConcreteTrickyLabelArrayType();

        $this->assertSame($postgresValue, $concreteTrickyLabelArrayType->convertToDatabaseValue([$trickyLabels], $this->platform));
    }

    #[DataProvider('provideLabelsNeedingEscaping')]
    #[Test]
    public function converts_labels_needing_escaping_to_php_value(TrickyLabels $trickyLabels, string $postgresValue): void
    {
        $concreteTrickyLabelArrayType = new ConcreteTrickyLabelArrayType();

        $this->assertSame([$trickyLabels], $concreteTrickyLabelArrayType->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{trickyLabels: TrickyLabels, postgresValue: string}>
     */
    public static function provideLabelsNeedingEscaping(): array
    {
        return [
            'plain label' => ['trickyLabels' => TrickyLabels::PLAIN, 'postgresValue' => '{"plain"}'],
            'label with a space' => ['trickyLabels' => TrickyLabels::WITH_SPACE, 'postgresValue' => '{"with space"}'],
            'label with a comma' => ['trickyLabels' => TrickyLabels::WITH_COMMA, 'postgresValue' => '{"with,comma"}'],
            'label with a double quote' => ['trickyLabels' => TrickyLabels::WITH_DOUBLE_QUOTE, 'postgresValue' => '{"with\"quote"}'],
            'label with a backslash' => ['trickyLabels' => TrickyLabels::WITH_BACKSLASH, 'postgresValue' => '{"with\\\\backslash"}'],
            'label with braces' => ['trickyLabels' => TrickyLabels::WITH_BRACES, 'postgresValue' => '{"{brace}"}'],
            'empty label' => ['trickyLabels' => TrickyLabels::EMPTY_LABEL, 'postgresValue' => '{""}'],
            'numeric-looking label' => ['trickyLabels' => TrickyLabels::NUMERIC_LOOKING, 'postgresValue' => '{"42"}'],
            'boolean-looking label' => ['trickyLabels' => TrickyLabels::BOOLEAN_LOOKING, 'postgresValue' => '{"true"}'],
            'lowercase null label' => ['trickyLabels' => TrickyLabels::LOWERCASE_NULL, 'postgresValue' => '{"null"}'],
        ];
    }

    #[DataProvider('provideUnquotedLabelsFromDatabase')]
    #[Test]
    public function converts_unquoted_labels_needing_no_escaping_to_php_value(TrickyLabels $trickyLabels, string $postgresValue): void
    {
        $concreteTrickyLabelArrayType = new ConcreteTrickyLabelArrayType();

        $this->assertSame([$trickyLabels], $concreteTrickyLabelArrayType->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{trickyLabels: TrickyLabels, postgresValue: string}>
     */
    public static function provideUnquotedLabelsFromDatabase(): array
    {
        return [
            'numeric-looking label' => ['trickyLabels' => TrickyLabels::NUMERIC_LOOKING, 'postgresValue' => '{42}'],
            'boolean-looking label' => ['trickyLabels' => TrickyLabels::BOOLEAN_LOOKING, 'postgresValue' => '{true}'],
            'plain label' => ['trickyLabels' => TrickyLabels::PLAIN, 'postgresValue' => '{plain}'],
        ];
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function converts_null_element_marker_from_database_to_null_item(): void
    {
        $result = $this->fixture->convertToPHPValue('{red,NULL}', $this->platform);

        $this->assertSame([Colors::RED, null], $result);
    }

    #[Test]
    public function returns_empty_array_for_empty_input(): void
    {
        $this->assertSame([], $this->fixture->convertToPHPValue('{}', $this->platform));
        $this->assertSame([], $this->fixture->convertToPHPValue('', $this->platform));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $this->fixture->transformArrayItemForPHP(123);
    }

    #[Test]
    public function throws_exception_when_unknown_enum_value_provided(): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $this->fixture->transformArrayItemForPHP('purple');
    }

    #[Test]
    public function throws_exception_for_malformed_enum_strings_in_database(): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue('{"purple"}', $this->platform);
    }

    #[Test]
    public function throws_exception_for_non_backed_enum_class_in_php_value(): void
    {
        $type = new class extends EnumArray {
            protected const TYPE_NAME = 'test_non_backed[]';

            protected function getEnumClass(): string
            {
                return \stdClass::class; // @phpstan-ignore-line
            }
        };

        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $type->convertToPHPValue('{red}', $this->platform);
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'label absent from the PHP enum' => ['{green}'],
            'empty label absent from the PHP enum' => ['{""}'],
            'multi-dimensional array' => ['{{red},{blue}}'],
            'unclosed quotes' => ['{"red}'],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueTypes')]
    #[Test]
    public function throws_exception_for_non_string_inputs_to_database_conversion(mixed $value): void
    {
        $this->expectException(InvalidEnumArrayItemForPHPException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueTypes(): array
    {
        return [
            'integer' => [123],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidEnumArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array of raw labels' => [['red', 'blue']],
            'array of integers' => [[1, 2]],
            'mixed array of valid and invalid items' => [[Colors::RED, 'blue']],
        ];
    }

    #[DataProvider('provideInvalidEnumArrayItems')]
    #[Test]
    public function throws_exception_for_invalid_enum_array_items(array $invalidArray): void
    {
        $this->expectException(InvalidEnumArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($invalidArray, $this->platform);
    }

    /**
     * @return array<string, array{array}>
     */
    public static function provideInvalidEnumArrayItems(): array
    {
        return [
            'integer item' => [[123]],
            'string item' => [['red']],
            'boolean item' => [[true]],
            'object item' => [[new \stdClass()]],
            'case of another enum' => [[Sizes::SMALL]],
            'mixed invalid items' => [[123, 'red', true]],
        ];
    }

    #[Test]
    public function throws_exception_for_case_of_another_enum(): void
    {
        $this->expectException(InvalidEnumArrayItemForDatabaseException::class);
        $this->expectExceptionMessage('Array items must be instances of '.Colors::class);

        $this->fixture->convertToDatabaseValue([Sizes::SMALL], $this->platform);
    }

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function validates_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'null' => [null],
            'first case' => [Colors::RED],
            'second case' => [Colors::BLUE],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function validates_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'raw label' => ['red'],
            'unknown label' => ['purple'],
            'integer' => [123],
            'empty string' => [''],
            'boolean' => [true],
            'case of another enum' => [Sizes::SMALL],
            'plain object' => [new \stdClass()],
        ];
    }
}
