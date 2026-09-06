<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Ulid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class UlidTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private Ulid $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new Ulid();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('ulid', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?string $phpValue, ?string $databaseValue): void
    {
        $this->assertSame($databaseValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?string $phpValue, ?string $databaseValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($databaseValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: string|null, databaseValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'databaseValue' => null,
            ],
            'canonical ULID' => [
                'phpValue' => '01ARZ3NDEKTSV4RRFFQ69G5FAV',
                'databaseValue' => '01ARZ3NDEKTSV4RRFFQ69G5FAV',
            ],
            'minimum ULID' => [
                'phpValue' => '00000000000000000000000000',
                'databaseValue' => '00000000000000000000000000',
            ],
            'maximum ULID' => [
                'phpValue' => '7ZZZZZZZZZZZZZZZZZZZZZZZZZ',
                'databaseValue' => '7ZZZZZZZZZZZZZZZZZZZZZZZZZ',
            ],
        ];
    }

    #[DataProvider('provideCaseNormalizations')]
    #[Test]
    public function normalizes_case_for_database_value(string $inputValue, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $this->fixture->convertToDatabaseValue($inputValue, $this->platform));
    }

    #[DataProvider('provideCaseNormalizations')]
    #[Test]
    public function normalizes_case_for_php_value(string $inputValue, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $this->fixture->convertToPHPValue($inputValue, $this->platform));
    }

    /**
     * @return array<string, array{inputValue: string, expectedValue: string}>
     */
    public static function provideCaseNormalizations(): array
    {
        return [
            'lowercase ULID' => [
                'inputValue' => '01arz3ndektsv4rrffq69g5fav',
                'expectedValue' => '01ARZ3NDEKTSV4RRFFQ69G5FAV',
            ],
            'mixed case ULID' => [
                'inputValue' => '01aRz3NdEkTsV4rRfFq69g5FaV',
                'expectedValue' => '01ARZ3NDEKTSV4RRFFQ69G5FAV',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidUlidForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [...self::provideInvalidTypeValues(), ...self::provideInvalidFormatValues()];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidUlidForPHPException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [...self::provideInvalidTypeValues(), ...self::provideInvalidFormatValues()];
    }

    /**
     * @return array<string, array{mixed}>
     */
    private static function provideInvalidTypeValues(): array
    {
        return [
            'integer input' => [42],
            'float input' => [3.14],
            'array input' => [['not', 'a', 'string']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    private static function provideInvalidFormatValues(): array
    {
        return [
            'empty string' => [''],
            'too short' => ['01ARZ3NDEKTSV4RRFFQ69G5FA'],
            'too long' => ['01ARZ3NDEKTSV4RRFFQ69G5FAV0'],
            'first character above 7' => ['81ARZ3NDEKTSV4RRFFQ69G5FAV'],
            'contains excluded letter I' => ['01ARZ3NDEKTSV4RRFFQ69G5FAI'],
            'contains excluded letter L' => ['01ARZ3NDEKTSV4RRFFQ69G5FAL'],
            'contains excluded letter O' => ['01ARZ3NDEKTSV4RRFFQ69G5FAO'],
            'contains excluded letter U' => ['01ARZ3NDEKTSV4RRFFQ69G5FAU'],
            'UUID instead of ULID' => ['550e8400-e29b-41d4-a716-446655440000'],
            'whitespace only' => ['                          '],
        ];
    }
}
