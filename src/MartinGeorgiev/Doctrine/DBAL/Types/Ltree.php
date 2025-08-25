<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;

final class Ltree extends BaseType
{
    protected const TYPE_NAME = 'ltree';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $this->assertPostgreSQLPlatform($platform);

        return parent::getSQLDeclaration($column, $platform);
    }

    #[\Override]
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        $this->assertPostgreSQLPlatform($platform);

        return [
            'ltree',
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?LtreeValueObject
    {
        $this->assertPostgreSQLPlatform($platform);

        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            try {
                return LtreeValueObject::fromString($value);
            } catch (\InvalidArgumentException) {
                throw InvalidLtreeForDatabaseException::forInvalidFormat($value);
            }
        }

        throw InvalidLtreeForDatabaseException::forInvalidType($value);
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        $this->assertPostgreSQLPlatform($platform);

        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            try {
                $value = LtreeValueObject::fromString($value);
            } catch (\InvalidArgumentException) {
                throw InvalidLtreeForPHPException::forInvalidFormat($value);
            }
        }

        if ($value instanceof LtreeValueObject) {
            return (string) $value;
        }

        throw InvalidLtreeForPHPException::forInvalidType($value);
    }

    private function assertPostgreSQLPlatform(AbstractPlatform $platform): void
    {
        $isDbalTwoPostgres = \is_a($platform, '\Doctrine\DBAL\Platforms\PostgreSqlPlatform');

        if ($platform instanceof PostgreSQLPlatform || $isDbalTwoPostgres) {
            return;
        }

        throw new \LogicException('Ltree DBAL type can only be used with the PostgreSQL platform.');
    }
}
