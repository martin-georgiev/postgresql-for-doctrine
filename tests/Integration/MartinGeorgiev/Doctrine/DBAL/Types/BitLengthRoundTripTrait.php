<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Shared length-aware round-trip tests for bit and bit varying types (scalars and arrays).
 *
 * Users must implement:
 * - getTypeName(): string
 * - getFieldDeclarationForLengthRoundTrip(): array — field declaration for the length-constrained column
 * - provideLengthRoundTripValues(): array — data provider returning [mixed $value] sets
 * - getValueExceedingLength(): mixed — a value whose width exceeds the declared length
 */
trait BitLengthRoundTripTrait
{
    abstract protected function getTypeName(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected static function getFieldDeclarationForLengthRoundTrip(): array;

    /**
     * @return array<string, array{mixed}>
     */
    abstract public static function provideLengthRoundTripValues(): array;

    /**
     * A value whose serialised width is larger than the declared length.
     * Used to prove the column enforces the declared length end-to-end.
     */
    abstract protected static function getValueExceedingLength(): mixed;

    #[DataProvider('provideLengthRoundTripValues')]
    #[Test]
    public function can_round_trip_with_explicit_length(mixed $value): void
    {
        [$tableName, $columnName] = $this->prepareLengthAwareTable();

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

    /**
     * Guards the length-aware `getSQLDeclaration()` contract end-to-end:
     * a regression that drops `(n)` from the declaration would create an
     * unconstrained column on bit varying types (silently accepting any width),
     * so we prove the constraint exists by asserting PostgreSQL rejects a value
     * whose width exceeds the declared length.
     */
    #[Test]
    public function rejects_value_exceeding_explicit_length(): void
    {
        [$tableName, $columnName] = $this->prepareLengthAwareTable();

        $this->expectException(DriverException::class);

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', static::getValueExceedingLength(), $this->getTypeName())
                ->executeStatement();
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array{string, string}
     */
    private function prepareLengthAwareTable(): array
    {
        $type = Type::getType($this->getTypeName());
        $platform = $this->connection->getDatabasePlatform();
        $columnType = $type->getSQLDeclaration(static::getFieldDeclarationForLengthRoundTrip(), $platform);

        return $this->prepareTestTable($columnType);
    }
}
