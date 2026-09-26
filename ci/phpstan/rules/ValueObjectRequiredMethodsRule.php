<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Keeps a value object's `__toString()` and static `fromString()` present, inherited or declared. Abstract bases are
 * exempt - `BaseGeometricValue` has no string form. Whether the two round-trip is semantic and unseeable here.
 *
 * @implements Rule<InClassNode>
 */
final class ValueObjectRequiredMethodsRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (!ValueObjectNamespace::isConcreteValueObject($classReflection)) {
            return [];
        }

        $className = $classReflection->getName();
        $errors = [];

        if (!$classReflection->hasMethod('__toString')) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Value object %s has no __toString(). It must render itself as the string PostgreSQL accepts.',
                $className
            ))
                ->identifier('martinGeorgiev.valueObject.missingToString')
                ->build();
        }

        if (!$classReflection->hasMethod('fromString')) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Value object %s has no fromString(). It must parse the string PostgreSQL produces.',
                $className
            ))
                ->identifier('martinGeorgiev.valueObject.missingFromString')
                ->build();

            return $errors;
        }

        $fromStringIsStatic = $classReflection->getNativeMethod('fromString')->isStatic();
        $fromStringIsPublic = $classReflection->getNativeMethod('fromString')->isPublic();
        if (!$fromStringIsStatic || !$fromStringIsPublic) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Value object %s declares fromString() as %s. It is the public static factory callers parse with.',
                $className,
                $fromStringIsStatic ? 'a non-public method' : 'a non-static method'
            ))
                ->identifier('martinGeorgiev.valueObject.fromStringNotAStaticFactory')
                ->build();
        }

        return $errors;
    }
}
