<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Fixtures\MartinGeorgiev\Doctrine\ConcreteInventoryItemArrayType;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCompositeArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CompositeArrayTest extends TestCase
{
    private PostgreSQLPlatform $platform;

    private ConcreteInventoryItemArrayType $fixture;

    protected function setUp(): void
    {
        $this->platform = new PostgreSQLPlatform();
        $this->fixture = new ConcreteInventoryItemArrayType();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('test_inventory_item[]', $this->fixture->getName());
    }

    #[Test]
    public function returns_sql_declaration_as_type_name(): void
    {
        $this->assertSame('test_inventory_item[]', $this->fixture->getSQLDeclaration([], $this->platform));
    }

    /**
     * @param array<int, array<string, mixed>|null>|null $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @param array<int, array<string, mixed>|null>|null $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: array<int, array<string, mixed>|null>|null, postgresValue: string|null}>
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
            'single item' => [
                'phpValue' => [['name' => 'widget', 'supplier_id' => 1, 'price' => '9.99']],
                'postgresValue' => '{"(widget,1,9.99)"}',
            ],
            'multiple items' => [
                'phpValue' => [
                    ['name' => 'widget', 'supplier_id' => 1, 'price' => '9.99'],
                    ['name' => 'bolt', 'supplier_id' => 2, 'price' => '0.50'],
                ],
                'postgresValue' => '{"(widget,1,9.99)","(bolt,2,0.50)"}',
            ],
            'null element' => [
                'phpValue' => [null, ['name' => 'bolt', 'supplier_id' => 2, 'price' => '0.50']],
                'postgresValue' => '{NULL,"(bolt,2,0.50)"}',
            ],
            'field holding a delimiter escapes at both levels' => [
                'phpValue' => [['name' => 'a,b', 'supplier_id' => 1, 'price' => '1.00']],
                'postgresValue' => '{"(\\"a,b\\",1,1.00)"}',
            ],
            // The composite layer doubles the quotes, the array layer then backslash-escapes each one
            'field holding a quote' => [
                'phpValue' => [['name' => 'say "hi"', 'supplier_id' => 1, 'price' => '1.00']],
                'postgresValue' => '{"(\\"say \\"\\"hi\\"\\"\\",1,1.00)"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $value): void
    {
        $this->expectException(InvalidCompositeArrayItemForPHPException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform); // @phpstan-ignore-line
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

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(array $value): void
    {
        $this->expectException(InvalidCompositeArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{array<int, mixed>}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string item' => [['not-a-composite']],
            'integer item' => [[123]],
            'boolean item' => [[true]],
            'object item' => [[new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidCompositeArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'not an array literal' => ['not-an-array-literal'],
            'item missing its closing parenthesis' => ['{"(widget,1"}'],
            'item that is not a record literal' => ['{"widget"}'],
            'multi-dimensional array literal' => ['{{"(a,1,1.00)"},{"(b,2,2.00)"}}'],
            'unclosed quotes in the array literal' => ['{"(widget,1,9.99)}'],
        ];
    }

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function validates_valid_array_item_for_database(mixed $item): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($item));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'null' => [null],
            'field-keyed array' => [['name' => 'widget', 'supplier_id' => 1, 'price' => '9.99']],
            'empty array' => [[]],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function validates_invalid_array_item_for_database(mixed $item): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($item));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'string' => ['(widget,1,9.99)'],
            'integer' => [123],
            'float' => [1.5],
            'boolean' => [true],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidCompositeArrayItemForPHPException::class);

        $this->fixture->transformArrayItemForPHP(123);
    }
}
