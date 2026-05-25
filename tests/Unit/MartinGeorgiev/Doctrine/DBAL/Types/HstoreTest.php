<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHstoreForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Hstore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class HstoreTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Hstore $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Hstore();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('hstore', $this->fixture->getName());
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
     * @param array<string, string|null> $input
     */
    #[DataProvider('provideArrayToHstoreConversions')]
    #[Test]
    public function converts_array_to_hstore_format(array $input, string $expected): void
    {
        $this->assertSame($expected, $this->fixture->convertToDatabaseValue($input, $this->platform));
    }

    /**
     * @return array<string, array{array<string, string|null>, string}>
     */
    public static function provideArrayToHstoreConversions(): array
    {
        return [
            'simple key-value pair' => [
                ['key' => 'value'],
                '"key"=>"value"',
            ],
            'multiple pairs with null value' => [
                ['a' => 'b', 'c' => null],
                '"a"=>"b","c"=>NULL',
            ],
            'value with double quotes' => [
                ['k' => 'v with "quotes"'],
                '"k"=>"v with \\"quotes\\""',
            ],
            'empty array' => [
                [],
                '',
            ],
        ];
    }

    #[DataProvider('provideHstoreToArrayConversions')]
    #[Test]
    public function parses_hstore_string_to_array(string $input, array $expected): void
    {
        $this->assertEquals($expected, $this->fixture->convertToPHPValue($input, $this->platform));
    }

    /**
     * @return array<string, array{string, array<string, string|null>}>
     */
    public static function provideHstoreToArrayConversions(): array
    {
        return [
            'single pair' => [
                '"key"=>"value"',
                ['key' => 'value'],
            ],
            'multiple pairs' => [
                '"a"=>"b","c"=>"d"',
                ['a' => 'b', 'c' => 'd'],
            ],
            'null value uppercase' => [
                '"a"=>"b","c"=>NULL',
                ['a' => 'b', 'c' => null],
            ],
            'null value lowercase' => [
                '"key"=>null',
                ['key' => null],
            ],
            'empty string value' => [
                '"key"=>""',
                ['key' => ''],
            ],
            'escaped double quote in value' => [
                '"k"=>"v with \\"quotes\\""',
                ['k' => 'v with "quotes"'],
            ],
            'escaped backslash in key' => [
                '"k\\\\ey"=>"val"',
                ['k\\ey' => 'val'],
            ],
            'whitespace around arrow' => [
                '"key" => "value"',
                ['key' => 'value'],
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseInputs')]
    #[Test]
    public function throws_exception_for_non_array_database_input(mixed $value): void
    {
        $this->expectException(InvalidHstoreForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseInputs(): array
    {
        return [
            'string input' => ['not an array'],
            'integer input' => [42],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPInputs')]
    #[Test]
    public function throws_exception_for_non_string_php_input(mixed $value): void
    {
        $this->expectException(InvalidHstoreForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['key' => 'value']],
            'object input' => [new \stdClass()],
            'boolean input' => [true],
        ];
    }
}
