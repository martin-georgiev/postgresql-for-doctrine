<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Keeps every dataset a provider spells out under a string key, so a failure names the case instead of an ordinal.
 * Rows merged in from a parent or spread in from another provider are already keyed where they were written.
 *
 * @implements Rule<Node\Stmt\ClassMethod>
 */
final class DataProviderDatasetKeyRule implements Rule
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
        if (!\str_starts_with($providerName, 'provide')) {
            return [];
        }

        $statements = $node->stmts ?? [];
        $nodeFinder = new NodeFinder();
        $errors = [];

        foreach ($nodeFinder->findInstanceOf($statements, Node\Stmt\Return_::class) as $return) {
            foreach ($this->datasetListsIn($return->expr) as $datasetList) {
                foreach ($datasetList->items as $item) {
                    if ($item->unpack || $item->key instanceof Node\Scalar\String_) {
                        continue;
                    }

                    $errors[] = $this->unnamedDataset($providerName, $item->getStartLine());
                }
            }
        }

        foreach ($nodeFinder->findInstanceOf($statements, Node\Expr\Yield_::class) as $yield) {
            if ($yield->key instanceof Node\Scalar\String_) {
                continue;
            }

            $errors[] = $this->unnamedDataset($providerName, $yield->getStartLine());
        }

        return $errors;
    }

    /**
     * The array literals holding datasets: the returned one, plus the ones handed to array_merge by a provider
     * that widens a parent's.
     *
     * @return list<Node\Expr\Array_>
     */
    private function datasetListsIn(?Node\Expr $expr): array
    {
        if ($expr instanceof Node\Expr\Array_) {
            return [$expr];
        }

        $isAMergeOfDatasetLists = $expr instanceof Node\Expr\FuncCall
            && $expr->name instanceof Node\Name
            && \in_array(\strtolower($expr->name->toString()), ['array_merge', 'array_replace'], true);
        if (!$isAMergeOfDatasetLists) {
            return [];
        }

        $datasetLists = [];
        foreach ($expr->args as $argument) {
            if ($argument instanceof Node\Arg) {
                foreach ($this->datasetListsIn($argument->value) as $datasetList) {
                    $datasetLists[] = $datasetList;
                }
            }
        }

        return $datasetLists;
    }

    private function unnamedDataset(string $providerName, int $line): IdentifierRuleError
    {
        return RuleErrorBuilder::message(\sprintf(
            'Data provider %s() has a dataset with no string key. Name the case, so PHPUnit reports it by name instead of by position.',
            $providerName
        ))
            ->identifier('martinGeorgiev.dataProvider.unnamedDataset')
            ->line($line)
            ->build();
    }
}
