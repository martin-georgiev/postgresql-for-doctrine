<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abstraction on top of Doctrine default Type class.
 *
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseType extends Type
{
    protected const TYPE_NAME = null;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        self::throwExceptionIfTypeNameNotConfigured();

        return $platform->getDoctrineTypeMapping(static::TYPE_NAME);
    }

    public function getName(): string
    {
        self::throwExceptionIfTypeNameNotConfigured();

        return static::TYPE_NAME;
    }

    private static function throwExceptionIfTypeNameNotConfigured(): void
    {
        if (null === static::TYPE_NAME) {
            throw new \LogicException(\sprintf('Doctrine type defined in class %s has no meaningful value for TYPE_NAME constant', self::class));
        }
    }
}
