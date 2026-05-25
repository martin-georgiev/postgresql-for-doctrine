<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ByteaTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'bytea';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
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
            'simple ascii string' => ['hello world'],
            'binary with null byte and high byte' => ["binary\x00data\xFF"],
            'deadbeef bytes' => ["\xDE\xAD\xBE\xEF"],
        ];
    }

    #[Test]
    public function can_handle_empty_bytea_as_null(): void
    {
        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $this->getTypeName(),
            $this->getPostgresTypeName(),
            '',
            null
        );
    }

    #[Test]
    public function can_read_raw_hex_bytea_inserted_directly(): void
    {
        $columnType = $this->getPostgresTypeName();
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $this->connection->executeStatement(
                \sprintf(
                    'INSERT INTO %s.%s ("%s") VALUES (?)',
                    self::DATABASE_SCHEMA,
                    $tableName,
                    $columnName
                ),
                [\hex2bin('68656c6c6f')],
                [ParameterType::BINARY]
            );

            $retrieved = $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);
            $this->assertSame('hello', $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
