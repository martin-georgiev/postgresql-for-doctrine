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
        return $platform->getDoctrineTypeMapping(static::TYPE_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return static::TYPE_NAME;
    }
}
