<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\LineArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class LineArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private LineArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new LineArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('line[]', $this->fixture->getName());
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
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<LineValueObject|null>|null,
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
            'single line' => [
                'phpValue' => [LineValueObject::fromString('{1,0,0}')],
                'postgresValue' => '{"{1,0,0}"}',
            ],
            'multiple lines' => [
                'phpValue' => [
                    LineValueObject::fromString('{1,0,0}'),
                    LineValueObject::fromString('{1.5,2.5,3.5}'),
                    LineValueObject::fromString('{-1,-2,-3}'),
                ],
                'postgresValue' => '{"{1,0,0}","{1.5,2.5,3.5}","{-1,-2,-3}"}',
            ],
            'array with null element' => [
                'phpValue' => [LineValueObject::fromString('{1,0,0}'), null],
                'postgresValue' => '{"{1,0,0}",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLineArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing non-value-object items' => [[1, 2, 3]],
            'invalid nested line' => [['{1,0,0}']],
            'mixed array (valid and invalid)' => [
                [
                    LineValueObject::fromString('{1,0,0}'),
                    'invalid',
                ],
            ],
            'array containing a boolean' => [[true]],
            'array containing a plain object' => [[new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['string value'],
            'integer instead of array' => [123],
            'object instead of array' => [new \stdClass()],
            'boolean instead of array' => [true],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'missing braces' => ['{"1,0,0"}'],
            'non-numeric values' => ['{"{abc,0,0}"}'],
            'invalid format' => ['{"not a line"}'],
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
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }

    #[DataProvider('provideMalformedInputs')]
    #[Test]
    public function throws_exception_for_malformed_array_literal(string $postgresValue): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedInputs(): array
    {
        return [
            'unparsable item' => ['{invalid}'],
            'quoted empty item' => ['{""}'],
            'not an array literal' => ['not-an-array'],
        ];
    }

    #[Test]
    public function throws_exception_when_invalid_line_format_provided(): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('(invalid,line)');
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
            'standard line' => [LineValueObject::fromString('{1,0,0}')],
            'decimal values' => [LineValueObject::fromString('{1.5,2.5,3.5}')],
            'negative coefficients' => [LineValueObject::fromString('{-1,-2,-3}')],
            'null' => [null],
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
            'string line format' => ['{1,0,0}'],
            'invalid string' => ['invalid'],
            'integer' => [123],
            'empty string' => [''],
            'boolean' => [true],
        ];
    }
}
