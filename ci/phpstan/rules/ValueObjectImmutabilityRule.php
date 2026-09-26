<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Keeps a value object `final` - or `@phpstan-consistent-constructor` when open by intent - with `private readonly`
 * properties; an abstract base may keep its state `protected`. Readonly subsumes a setter ban, and
 * `ReadOnlyClassRector` only rewrites a class already final and all-readonly, so it guarantees none of this.
 *
 * @implements Rule<InClassNode>
 */
final class ValueObjectImmutabilityRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (!ValueObjectNamespace::isValueObject($classReflection)) {
            return [];
        }

        $className = $classReflection->getName();
        $nativeReflection = $classReflection->getNativeReflection();
        $isAbstractBase = $classReflection->isAbstract();

        $errors = [];

        $isClosedForExtension = $nativeReflection->isFinal()
            || ValueObjectNamespace::isDesignedForExtension($classReflection);
        if (!$isAbstractBase && !$isClosedForExtension) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Value object %s must be declared final, or marked @phpstan-consistent-constructor when it is deliberately open for extension.',
                $className
            ))
                ->identifier('martinGeorgiev.valueObject.notFinal')
                ->build();
        }

        $isReadOnlyClass = $nativeReflection->isReadOnly();

        foreach ($nativeReflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass()->getName() !== $className || $reflectionProperty->isStatic()) {
                continue;
            }

            if (!$isReadOnlyClass && !$reflectionProperty->isReadOnly()) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Value object %s declares mutable property $%s. Every property of a value object is readonly.',
                    $className,
                    $reflectionProperty->getName()
                ))
                    ->identifier('martinGeorgiev.valueObject.mutableProperty')
                    ->build();
            }

            $hasAcceptableVisibility = $reflectionProperty->isPrivate() || ($isAbstractBase && $reflectionProperty->isProtected());
            if (!$hasAcceptableVisibility) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Value object %s exposes property $%s. Declare it private%s.',
                    $className,
                    $reflectionProperty->getName(),
                    $isAbstractBase ? ', or protected when subclasses read it' : ''
                ))
                    ->identifier('martinGeorgiev.valueObject.exposedProperty')
                    ->build();
            }
        }

        return $errors;
    }
}
