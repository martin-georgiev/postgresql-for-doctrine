<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Shared length-aware round-trip tests for bit and bit varying types (scalars and arrays).
 *
 * Users must implement:
 * - getTypeName(): string
 * - getLengthColumnType(): string — e.g. 'BIT(3)', 'BIT VARYING(5)', 'BIT(3)[]', 'BIT VARYING(5)[]'
 * - provideLengthRoundTripValues(): array — data provider returning [mixed $value] sets
 */
trait BitLengthRoundTripTrait
{
    abstract protected function getTypeName(): string;

    abstract protected static function getLengthColumnType(): string;

    /**
     * @return array<string, array{mixed}>
     */
    abstract public static function provideLengthRoundTripValues(): array;

    #[DataProvider('provideLengthRoundTripValues')]
    #[Test]
    public function can_round_trip_with_explicit_length(mixed $value): void
    {
        [$tableName, $columnName] = $this->prepareTestTable(static::getLengthColumnType());

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $value, $this->getTypeName())
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);
            $this->assertSame($value, $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
