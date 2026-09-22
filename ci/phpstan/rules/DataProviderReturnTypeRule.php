<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\MixedType;
use PHPStan\Type\VerbosityLevel;
use PHPUnit\Framework\TestCase;

/**
 * Keeps a provider's datasets described by a `@return`, so a row that drifts from the signature it feeds is a static
 * error rather than a runtime one. `missingType.iterableValue` is ignored repo-wide, so level max never sees these.
 *
 * @implements Rule<Node\Stmt\ClassMethod>
 */
final class DataProviderReturnTypeRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection || !$classReflection->is(TestCase::class)) {
            return [];
        }

        $providerName = $node->name->toString();
        if (!\str_starts_with($providerName, 'provide') || !$classReflection->hasMethod($providerName)) {
            return [];
        }

        $variants = $classReflection->getMethod($providerName, $scope)->getVariants();
        $returnType = $variants[0]->getReturnType();

        if (!$returnType->isIterable()->yes()) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Data provider %s() returns %s. A provider hands PHPUnit its datasets as an array, a \Generator or an iterable.',
                    $providerName,
                    $returnType->describe(VerbosityLevel::typeOnly())
                ))
                    ->identifier('martinGeorgiev.dataProvider.notIterable')
                    ->build(),
            ];
        }

        $datasetType = $returnType->getIterableValueType();
        $datasetsAreDescribed = !($datasetType instanceof MixedType) || $datasetType->isExplicitMixed();
        if ($datasetsAreDescribed) {
            return [];
        }

        return [
            RuleErrorBuilder::message(\sprintf(
                'Data provider %s() does not describe its datasets. Give it a @return naming the key and the shape of one row.',
                $providerName
            ))
                ->identifier('martinGeorgiev.dataProvider.undescribedDatasets')
                ->build(),
        ];
    }
}
