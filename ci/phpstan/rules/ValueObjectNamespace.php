<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PHPStan\Reflection\ClassReflection;

/**
 * Tells the value object rules which classes they own. The namespace also holds enums, whose implicit `$name`/`$value`
 * read as public mutable state, abstract bases carrying shared state, and the nested `Exceptions\`.
 */
final class ValueObjectNamespace
{
    /**
     * @var string
     */
    public const VALUE_OBJECTS = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\ValueObject\\';

    /**
     * @var string
     */
    public const EXCEPTIONS = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\ValueObject\\Exceptions\\';

    /**
     * A PostgreSQL value, abstract bases included.
     */
    public static function isValueObject(ClassReflection $classReflection): bool
    {
        if ($classReflection->isAnonymous()) {
            return false;
        }

        $isAClassRatherThanAnEnumInterfaceOrTrait = $classReflection->isClass()
            && !$classReflection->isEnum()
            && !$classReflection->isInterface()
            && !$classReflection->isTrait();
        if (!$isAClassRatherThanAnEnumInterfaceOrTrait) {
            return false;
        }

        $name = $classReflection->getName();

        return \str_starts_with($name, self::VALUE_OBJECTS)
            && !\str_starts_with($name, self::EXCEPTIONS);
    }

    /**
     * The shape the conventions describe.
     */
    public static function isConcreteValueObject(ClassReflection $classReflection): bool
    {
        return self::isValueObject($classReflection) && !$classReflection->isAbstract();
    }

    /**
     * Deliberately open for extension, as `Ltree` and `Interval` are.
     */
    public static function isDesignedForExtension(ClassReflection $classReflection): bool
    {
        $docComment = $classReflection->getNativeReflection()->getDocComment();

        return \is_string($docComment) && \str_contains($docComment, '@phpstan-consistent-constructor');
    }
}
