<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Utils\PHPArrayToPostgresValueTransformer;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL TEXT[] data type.
 *
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * @since 0.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TextArray extends BaseType
{
    /**
     * @var string
     */
    public const TYPE_NAME = 'text[]';

    /**
     * Converts a value from its PHP representation to its database representation of the type.
     *
     * @param array|null $value the value to convert
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->transformToPostgresTextArray($value);
    }

    protected function transformToPostgresTextArray(array $phpTextArray): string
    {
        if ($phpTextArray === []) {
            return '{}';
        }

        return PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($phpTextArray);
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param string|null $value the value to convert
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null) {
            return null;
        }

        return $this->transformFromPostgresTextArray($value);
    }

    protected function transformFromPostgresTextArray(string $postgresValue): array
    {
        $values = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue);

        // No matter what the original PHP array items' data types were,
        // once they are stored in PostgreSQL, all of them will become strings.
        // Therefore, we need to ensure all items in the returned PHP array are strings.
        foreach ($values as $key => $value) {
            if (\is_string($value)) {
                continue;
            }

            if (\is_bool($value)) {
                $values[$key] = $value ? 'true' : 'false';

                continue;
            }

            if ($value === null) {
                $values[$key] = 'null';

                continue;
            }

            $values[$key] = (string) $value; // @phpstan-ignore-line
        }

        return $values;
    }
}
