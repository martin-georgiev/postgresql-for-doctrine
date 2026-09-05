<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\VarcharArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class VarcharArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private VarcharArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new VarcharArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('varchar[]', $this->fixture->getName());
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
            'single string' => [
                'phpValue' => ['hello'],
                'postgresValue' => '{"hello"}',
            ],
            'multiple strings' => [
                'phpValue' => ['foo', 'bar', 'baz'],
                'postgresValue' => '{"foo","bar","baz"}',
            ],
            'strings with special characters' => [
                'phpValue' => ['with,comma', 'with "double-quotes"'],
                'postgresValue' => '{"with,comma","with \"double-quotes\""}',
            ],
            'numbers as strings' => [
                'phpValue' => ['1', '2.5'],
                'postgresValue' => '{"1","2.5"}',
            ],
        ];
    }

    #[Test]
    public function converts_unquoted_postgres_array_to_php_value(): void
    {
        $postgresValue = '{STRING_A,STRING_B,STRING_C,STRING_D}';
        $expectedValue = ['STRING_A', 'STRING_B', 'STRING_C', 'STRING_D'];

        $this->assertSame($expectedValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    #[DataProvider('provideStringPreservationTestCases')]
    #[Test]
    public function preserves_string_types_retrieved_from_database(string $postgresValue, array $expectedResult): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertSame($expectedResult, $result);

        foreach ($result as $value) {
            $this->assertIsString($value);
        }
    }

    /**
     * PostgreSQL omits quotes around varchar array items that look numeric or
     * boolean; these must still come back as PHP strings (see GitHub issue #424).
     *
     * @return array<string, array{
     *     postgresValue: string,
     *     expectedResult: array<string>
     * }>
     */
    public static function provideStringPreservationTestCases(): array
    {
        return [
            'numeric values should be preserved as strings' => [
                'postgresValue' => '{1,test}',
                'expectedResult' => ['1', 'test'],
            ],
            'mixed values should be preserved as strings' => [
                'postgresValue' => '{1,2.5,3.14,test,true,false}',
                'expectedResult' => ['1', '2.5', '3.14', 'test', 'true', 'false'],
            ],
            'quoted boolean-like values should remain as strings' => [
                'postgresValue' => '{"true","false","t","f"}',
                'expectedResult' => ['true', 'false', 't', 'f'],
            ],
        ];
    }

    #[Test]
    public function preserves_trailing_zeros_in_strings_that_look_like_decimals(): void
    {
        $postgresValue = '{42.00,123.50,0.00,999.99,502.00,505.00}';
        $expectedResult = ['42.00', '123.50', '0.00', '999.99', '502.00', '505.00'];

        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertSame($expectedResult, $result);
    }
}
