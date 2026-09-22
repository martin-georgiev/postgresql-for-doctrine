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

        $errors = [];

        foreach ($this->ownStatementsOf($node) as $statement) {
            if ($statement instanceof Node\Stmt\Return_) {
                foreach ($this->datasetListsIn($statement->expr) as $datasetList) {
                    foreach ($datasetList->items as $item) {
                        if ($item->unpack || $this->isAStringKey($item->key, $scope)) {
                            continue;
                        }

                        $errors[] = $this->unnamedDataset($providerName, $item->getStartLine());
                    }
                }
            }

            if ($statement instanceof Node\Expr\Yield_ && !$this->isAStringKey($statement->key, $scope)) {
                $errors[] = $this->unnamedDataset($providerName, $statement->getStartLine());
            }
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

    /**
     * The provider's own returns and yields. A closure written inside it carries its own, which belong to it.
     *
     * @return list<Node>
     */
    private function ownStatementsOf(Node\Stmt\ClassMethod $classMethod): array
    {
        $nodeFinder = new NodeFinder();
        $statements = $classMethod->stmts ?? [];

        $nestedInAClosure = [];
        foreach ($nodeFinder->findInstanceOf($statements, Node\FunctionLike::class) as $functionLike) {
            foreach ($this->returnsAndYieldsIn($nodeFinder, $functionLike) as $nested) {
                $nestedInAClosure[\spl_object_id($nested)] = true;
            }
        }

        $own = [];
        foreach ($this->returnsAndYieldsIn($nodeFinder, $statements) as $node) {
            if (!isset($nestedInAClosure[\spl_object_id($node)])) {
                $own[] = $node;
            }
        }

        return $own;
    }

    /**
     * @param array<array-key, Node>|Node $nodes
     *
     * @return list<Node>
     */
    private function returnsAndYieldsIn(NodeFinder $nodeFinder, array|Node $nodes): array
    {
        return \array_values(\array_merge(
            $nodeFinder->findInstanceOf($nodes, Node\Stmt\Return_::class),
            $nodeFinder->findInstanceOf($nodes, Node\Expr\Yield_::class)
        ));
    }

    private function isAStringKey(?Node\Expr $expr, Scope $scope): bool
    {
        if (!$expr instanceof Node\Expr) {
            return false;
        }

        return $scope->getType($expr)->isString()->yes();
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
