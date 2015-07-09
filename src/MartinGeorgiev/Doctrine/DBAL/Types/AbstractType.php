<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

class AbstractType extends \Doctrine\DBAL\Types\Type
{
    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
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
