<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidByteaForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ByteaTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bytea';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BYTEA';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'ASCII string' => ['Hello'],
            'binary data' => ["\xff\x00\xab"],
            'null byte' => ["\x00"],
            'empty-ish binary' => ["\x01\x02\x03"],
        ];
    }

    #[DataProvider('provideInvalidInputs')]
    #[Test]
    public function rejects_invalid_inputs_before_database_write(mixed $value): void
    {
        $this->expectException(InvalidByteaForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['data']],
        ];
    }
}
