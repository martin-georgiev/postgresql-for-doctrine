<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Keeps every data provider under the `provide*` prefix, so the suite can be grepped for an existing dataset by name
 * before a duplicate is written. PHPUnit itself accepts any method name, which is what lets the prefix drift.
 *
 * @implements Rule<Node\Stmt\ClassMethod>
 */
final class DataProviderNamingRule implements Rule
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

        $errors = [];

        foreach (DataProviderAttribute::referencesIn($node) as [$providerName, $line]) {
            if (\str_starts_with($providerName, 'provide')) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Data provider %s() feeding %s() is not named provide*. Every dataset method carries the prefix so it can be found before one is written twice.',
                $providerName,
                $node->name->toString()
            ))
                ->identifier('martinGeorgiev.dataProvider.naming')
                ->line($line)
                ->build();
        }

        return $errors;
    }
}
