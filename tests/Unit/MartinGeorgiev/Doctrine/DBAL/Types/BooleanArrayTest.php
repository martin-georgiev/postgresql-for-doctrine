<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BooleanArrayTest extends TestCase
{
    private PostgreSQLPlatform $platform;

    private BooleanArray $fixture;

    protected function setUp(): void
    {
        $this->platform = new PostgreSQLPlatform();

        $this->fixture = new BooleanArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bool[]', $this->fixture->getName());
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
            'mixed boolean array' => [
                'phpValue' => [true, false, true],
                'postgresValue' => '{1,0,1}',
            ],
            'array with a null element' => [
                'phpValue' => [true, null, false],
                'postgresValue' => '{1,NULL,0}',
            ],
            'array of only a null element' => [
                'phpValue' => [null],
                'postgresValue' => '{NULL}',
            ],
        ];
    }

    #[DataProvider('providePostgresWrittenValues')]
    #[Test]
    public function converts_postgres_written_value_to_php_value(string $postgresValue, array $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedResult: array<int, bool|null>}>
     */
    public static function providePostgresWrittenValues(): array
    {
        return [
            'unquoted items' => ['postgresValue' => '{t,f}', 'expectedResult' => [true, false]],
            'quoted items' => ['postgresValue' => '{"t","f"}', 'expectedResult' => [true, false]],
            'null element among values' => ['postgresValue' => '{t,f,NULL}', 'expectedResult' => [true, false, null]],
            'only a null element' => ['postgresValue' => '{NULL}', 'expectedResult' => [null]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
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
            'boolean true' => [true],
            'boolean false' => [false],
            'null' => [null],
            'integer' => [1],
            'string' => ['value'],
        ];
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }
}
