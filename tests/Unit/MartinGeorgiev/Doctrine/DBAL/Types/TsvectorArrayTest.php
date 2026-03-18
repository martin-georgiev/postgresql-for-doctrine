<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsvectorArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TsvectorArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TsvectorArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TsvectorArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TsvectorArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('tsvector[]', $this->fixture->getName());
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
            'single tsvector' => [
                'phpValue' => ["'cat' 'dog'"],
                'postgresValue' => '{"\'cat\' \'dog\'"}',
            ],
            'multiple tsvectors' => [
                'phpValue' => ["'cat' 'dog'", "'tree'"],
                'postgresValue' => '{"\'cat\' \'dog\'","\'tree\'"}',
            ],
            'tsvector with positions' => [
                'phpValue' => ["'cat':1 'dog':2"],
                'postgresValue' => '{"\'cat\':1 \'dog\':2"}',
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
        $this->expectException(InvalidTsvectorArrayItemForDatabaseException::class);
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
            'object item' => [[new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidTsvectorArrayItemForPHPException::class);
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
            'simple tsvector' => ["'cat'"],
            'tsvector with multiple lexemes' => ["'cat' 'dog'"],
            'tsvector with positions' => ["'cat':1 'dog':2"],
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
        $this->expectException(InvalidTsvectorArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
