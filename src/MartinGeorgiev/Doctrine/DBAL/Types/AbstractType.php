<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Abstraction on top of Doctrine default Type class
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class AbstractType extends Type
{
    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        self::throwExceptionIfTypeNameNotConfigured();

        return $platform->getDoctrineTypeMapping(constant('static::TYPE_NAME'));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        self::throwExceptionIfTypeNameNotConfigured();

        return constant('static::TYPE_NAME');
    }

    private static function throwExceptionIfTypeNameNotConfigured()
    {
        if (false === defined('static::TYPE_NAME')) {
            throw new \LogicException(sprintf('Doctrine type defined in class %s is missing the TYPE_NAME constant', self::class));
        }
    }
}
