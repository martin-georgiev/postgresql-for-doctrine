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
    /**
     * @var string
     */
    protected const TYPE_NAME = '';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $this->throwExceptionIfTypeNameNotConfigured();

        return $platform->getDoctrineTypeMapping(static::TYPE_NAME);
    }

    public function getName(): string
    {
        $this->throwExceptionIfTypeNameNotConfigured();

        return static::TYPE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return false;
    }

    private function throwExceptionIfTypeNameNotConfigured(): void
    {
        if (static::TYPE_NAME === '') {
            throw new \LogicException(\sprintf('Doctrine type defined in class %s has no meaningful value for TYPE_NAME constant', self::class));
        }
    }
}
