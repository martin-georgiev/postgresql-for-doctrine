<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;

/**
 * Reads the provider method name out of a `#[DataProvider]` or `#[DataProviderExternal]` attribute - the external
 * form names its class first, so the method sits in the second argument.
 */
final class DataProviderAttribute
{
    /**
     * @return list<array{string, int}> the referenced method name and the line it is referenced on
     */
    public static function referencesIn(Node\Stmt\ClassMethod $classMethod): array
    {
        $references = [];

        foreach ($classMethod->attrGroups as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attribute) {
                $attributeName = $attribute->name->toString();
                if ($attributeName === DataProvider::class) {
                    $argumentIndex = 0;
                } elseif ($attributeName === DataProviderExternal::class) {
                    $argumentIndex = 1;
                } else {
                    continue;
                }

                $argument = self::methodNameArgument($attribute->args, $argumentIndex);
                if ($argument instanceof Node\Arg && $argument->value instanceof Node\Scalar\String_) {
                    $references[] = [$argument->value->value, $attribute->getStartLine()];
                }
            }
        }

        return $references;
    }

    /**
     * Both attributes name the method `methodName`, so a named argument finds it whatever its position.
     *
     * @param array<int, Node\Arg|Node\VariadicPlaceholder> $arguments
     */
    private static function methodNameArgument(array $arguments, int $argumentIndex): ?Node\Arg
    {
        foreach ($arguments as $argument) {
            if ($argument instanceof Node\Arg && $argument->name?->toString() === 'methodName') {
                return $argument;
            }
        }

        $positional = [];
        foreach ($arguments as $argument) {
            if ($argument instanceof Node\Arg && !$argument->name instanceof Node\Identifier) {
                $positional[] = $argument;
            }
        }

        return $positional[$argumentIndex] ?? null;
    }
}
