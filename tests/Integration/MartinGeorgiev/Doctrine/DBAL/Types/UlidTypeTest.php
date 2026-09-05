<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class UlidTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('ulid');
    }

    protected function getTypeName(): string
    {
        return 'ulid';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'canonical ULID' => ['01ARZ3NDEKTSV4RRFFQ69G5FAV'],
            'minimum ULID' => ['00000000000000000000000000'],
            'maximum ULID' => ['7ZZZZZZZZZZZZZZZZZZZZZZZZZ'],
        ];
    }

    #[Test]
    public function roundtrips_lowercase_input_as_uppercase(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($typeName, $columnType, '01arz3ndektsv4rrffq69g5fav', '01ARZ3NDEKTSV4RRFFQ69G5FAV');
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidUlidForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            'empty string' => [''],
            'too short' => ['01ARZ3NDEKTSV4RRFFQ69G5FA'],
            'first character above 7' => ['81ARZ3NDEKTSV4RRFFQ69G5FAV'],
            'integer' => [42],
        ];
    }
}
