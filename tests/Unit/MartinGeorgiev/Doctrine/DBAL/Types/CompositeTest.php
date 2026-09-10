<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInventoryItemType;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CompositeTest extends TestCase
{
    private PostgreSQLPlatform $platform;

    private ConcreteInventoryItemType $fixture;

    protected function setUp(): void
    {
        $this->platform = new PostgreSQLPlatform();
        $this->fixture = new ConcreteInventoryItemType();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('test_inventory_item', $this->fixture->getName());
    }

    #[Test]
    public function returns_sql_declaration_as_type_name(): void
    {
        $this->assertSame('test_inventory_item', $this->fixture->getSQLDeclaration([], $this->platform));
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    /**
     * @param array<string, mixed> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @param array<string, mixed> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: array<string, mixed>, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'plain values' => [
                'phpValue' => ['name' => 'widget', 'supplier_id' => 42, 'price' => '9.99'],
                'postgresValue' => '(widget,42,9.99)',
            ],
            'all fields null' => [
                'phpValue' => ['name' => null, 'supplier_id' => null, 'price' => null],
                'postgresValue' => '(,,)',
            ],
            'empty string then nulls' => [
                'phpValue' => ['name' => '', 'supplier_id' => null, 'price' => null],
                'postgresValue' => '("",,)',
            ],
            'value containing a comma' => [
                'phpValue' => ['name' => 'a,b', 'supplier_id' => 1, 'price' => '1'],
                'postgresValue' => '("a,b",1,1)',
            ],
            'numeric scale is preserved' => [
                'phpValue' => ['name' => 'x', 'supplier_id' => 1, 'price' => '9.90'],
                'postgresValue' => '(x,1,9.90)',
            ],
        ];
    }

    #[DataProvider('provideAlternativeDatabaseEncodings')]
    #[Test]
    public function parses_alternative_database_encodings(string $postgresValue, string $expectedName): void
    {
        $converted = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertIsArray($converted);
        $this->assertSame($expectedName, $converted['name']);
    }

    /**
     * @return array<string, array{postgresValue: string, expectedName: string}>
     */
    public static function provideAlternativeDatabaseEncodings(): array
    {
        return [
            'backslash escape inside quotes' => ['postgresValue' => '("a\\,b",1,1)', 'expectedName' => 'a,b'],
            'backslash escape outside quotes' => ['postgresValue' => '(a\\,b,1,1)', 'expectedName' => 'a,b'],
            'backslash escaped quote inside quotes' => ['postgresValue' => '("a\\"b",1,1)', 'expectedName' => 'a"b'],
            'whitespace around the parentheses' => ['postgresValue' => '  (widget,1,1)  ', 'expectedName' => 'widget'],
            'unquoted whitespace stays significant' => ['postgresValue' => '(  ,1,1)', 'expectedName' => '  '],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidCompositeForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string instead of array' => ['(widget,42,9.99)'],
            'integer' => [42],
            'boolean' => [true],
            'object' => [new \stdClass()],
            'missing a declared field' => [['name' => 'widget', 'supplier_id' => 42]],
            'carrying an undeclared field' => [['name' => 'widget', 'supplier_id' => 42, 'price' => '1', 'color' => 'red']],
            'empty array' => [[]],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidCompositeForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer' => [42],
            'array' => [['widget', 42, '9.99']],
            'object' => [new \stdClass()],
            'boolean' => [true],
            'missing the opening parenthesis' => ['widget,42,9.99)'],
            'missing the closing parenthesis' => ['(widget,42,9.99'],
            'unterminated quoted field' => ['("widget,42,9.99)'],
            'trailing backslash' => ['(widget,42,9.99\\'],
            'too few fields' => ['(widget,42)'],
            'too many fields' => ['(widget,42,9.99,extra)'],
        ];
    }

    #[Test]
    public function throws_exception_for_field_value_that_cannot_be_stringified(): void
    {
        $this->expectException(InvalidCompositeForDatabaseException::class);

        $this->fixture->convertToDatabaseValue(
            ['name' => 'widget', 'supplier_id' => 42, 'price' => ['not', 'a', 'scalar']],
            $this->platform
        );
    }
}
