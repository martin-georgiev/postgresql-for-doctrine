<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\LtreeInterface;

final class Ltree extends BaseType
{
    protected const TYPE_NAME = 'ltree';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $this->assertPostgreSQLPlatform($platform);

        return 'ltree';
    }

    #[\Override]
    public function getBindingType(): ParameterType
    {
        return ParameterType::STRING;
    }

    #[\Override]
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
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

        if ($value instanceof LtreeInterface) {
            return (string) $value;
        }

        if (null === $value) {
            return null;
        }

        throw InvalidLtreeForPHPException::forInvalidType($value);
    }

    private function assertPostgreSQLPlatform(AbstractPlatform $platform): void
    {
        if (!$platform instanceof PostgreSQLPlatform) {
            throw new \InvalidArgumentException('LtreeType can only be used with PostgreSQL platform.');
        }
    }
}
