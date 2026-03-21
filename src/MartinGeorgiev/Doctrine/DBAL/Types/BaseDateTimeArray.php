<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Base class for PostgreSQL datetime array types (DATE[], TIMESTAMP[], TIMESTAMPTZ[]).
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseDateTimeArray extends BaseArray
{
    /**
     * Returns the format string for serializing to PostgreSQL.
     */
    abstract protected function getPostgresFormat(): string;

    /**
     * Returns format strings for parsing from PostgreSQL, tried in order.
     *
     * @return non-empty-list<string>
     */
    abstract protected function getPHPFormats(): array;

    protected function transformParsedValueForPHP(\DateTimeImmutable $value): \DateTimeImmutable
    {
        return $value;
    }

    abstract protected function throwInvalidPHPTypeException(mixed $item): never;

    abstract protected function throwInvalidPHPFormatException(mixed $item): never;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        return $item instanceof \DateTimeInterface;
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert($item instanceof \DateTimeInterface);

        return '"'.$item->format($this->getPostgresFormat()).'"';
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?\DateTimeImmutable
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            $this->throwInvalidPHPTypeException($item);
        }

        foreach ($this->getPHPFormats() as $format) {
            $parsed = \DateTimeImmutable::createFromFormat($format, $item);
            if ($parsed !== false) {
                return $this->transformParsedValueForPHP($parsed);
            }
        }

        $this->throwInvalidPHPFormatException($item);
    }
}
