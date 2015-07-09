<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class AbstractType extends Type
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
