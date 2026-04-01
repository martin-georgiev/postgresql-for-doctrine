<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
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
    protected const TYPE_NAME = Type::TEXT_ARRAY;

    /**
     * @param array|null $value
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
     * @param string|null $value
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
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray(
            $postgresValue,
            preserveStringTypes: true
        );
    }
}
