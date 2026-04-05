<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsqueryArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TsqueryArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TsqueryArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TsqueryArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TsqueryArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('tsquery[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array|null,
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
            'single tsquery' => [
                'phpValue' => ["'cat'"],
                'postgresValue' => '{"\'cat\'"}',
            ],
            'multiple tsqueries' => [
                'phpValue' => ["'cat'", "'dog'"],
                'postgresValue' => '{"\'cat\'","\'dog\'"}',
            ],
            'tsquery with AND operator' => [
                'phpValue' => ["'cat' & 'dog'"],
                'postgresValue' => '{"\'cat\' & \'dog\'"}',
            ],
            'tsquery with OR operator' => [
                'phpValue' => ["'tree' | 'plant'"],
                'postgresValue' => '{"\'tree\' | \'plant\'"}',
            ],
            'tsquery with NOT operator' => [
                'phpValue' => ["!'cat'"],
                'postgresValue' => '{"!\'cat\'"}',
            ],
            'array with null item' => [
                'phpValue' => [null, "'cat'"],
                'postgresValue' => '{NULL,"\'cat\'"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidTsqueryArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'empty string item' => [['']],
            'integer item' => [[123]],
            'boolean item' => [[true]],
            'mixed valid and invalid' => [["'cat'", '']],
            'valid mixed with integer' => [["'cat'", 123]],
            'valid mixed with boolean' => [["'cat'", false]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidTsqueryArrayItemForPHPException::class);
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

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'simple tsquery' => ["'cat'"],
            'tsquery with AND' => ["'cat' & 'dog'"],
            'tsquery with OR' => ["'tree' | 'plant'"],
            'tsquery with NOT' => ["!'cat'"],
            'null value' => [null],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'empty string' => [''],
            'integer' => [123],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function can_transform_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidTsqueryArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }

    #[Test]
    public function can_convert_array_with_null_to_database(): void
    {
        $phpValue = ["'cat'", null, "'dog'"];
        $expected = '{"\'cat\'",NULL,"\'dog\'"}';

        $this->assertSame($expected, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }
}
