<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Base class for PostgreSQL array types that store string values.
 *
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseStringArray extends BaseArray
{
    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_string($item));

        return $this->quoteAndEscapeArrayItem($item);
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw $this->createInvalidTypeExceptionForPHP($item);
        }

        return $item;
    }

    abstract protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException;
}
