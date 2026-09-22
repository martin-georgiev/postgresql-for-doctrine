<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

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

                $argument = $attribute->args[$argumentIndex] ?? null;
                if ($argument instanceof Node\Arg && $argument->value instanceof Node\Scalar\String_) {
                    $references[] = [$argument->value->value, $attribute->getStartLine()];
                }
            }
        }

        return $references;
    }
}
