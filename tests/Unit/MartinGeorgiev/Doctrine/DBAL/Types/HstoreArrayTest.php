<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\HstoreArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class HstoreArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private HstoreArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new HstoreArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('hstore[]', $this->fixture->getName());
    }

    /**
     * @param array<int, array<string, string|null>|null>|null $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @param array<int, array<string, string|null>|null>|null $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<int, array<string, string|null>|null>|null,
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
            'single hstore' => [
                'phpValue' => [['a' => 'b']],
                'postgresValue' => '{"\"a\"=>\"b\""}',
            ],
            'hstore with two pairs' => [
                'phpValue' => [['a' => 'b', 'c' => 'd']],
                'postgresValue' => '{"\"a\"=>\"b\",\"c\"=>\"d\""}',
            ],
            'hstore with null value' => [
                'phpValue' => [['key' => null]],
                'postgresValue' => '{"\"key\"=>NULL"}',
            ],
            'null item in array' => [
                'phpValue' => [['a' => 'b'], null, ['c' => 'd']],
                'postgresValue' => '{"\"a\"=>\"b\"",NULL,"\"c\"=>\"d\""}',
            ],
            'empty hstore item' => [
                'phpValue' => [[]],
                'postgresValue' => '{""}',
            ],
            'multiple hstores' => [
                'phpValue' => [['a' => 'b'], ['c' => 'd']],
                'postgresValue' => '{"\"a\"=>\"b\"","\"c\"=>\"d\""}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidHstoreArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string item' => [['hello']],
            'integer item' => [[123]],
            'object item' => [[new \stdClass()]],
            'hstore item with integer value' => [['key' => 123]],
            'hstore item with boolean value' => [['key' => true]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidHstoreArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
        ];
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
            'null value' => [null],
            'empty hstore' => [[]],
            'simple hstore' => [['a' => 'b']],
            'hstore with null value' => [['key' => null]],
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
            'string' => ['hello'],
            'integer' => [123],
            'float' => [3.14],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidHstoreArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer element' => ['{42}'],
            'boolean element' => ['{true}'],
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
        $this->expectException(InvalidHstoreArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
