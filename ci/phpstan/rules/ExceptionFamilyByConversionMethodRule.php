<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Holds a DBAL type's four conversion methods to the exception family named after the direction they convert in.
 * A method reading a database value raises the `ForPHP` family, one writing a database value the `ForDatabase` one.
 * Inverting the pair still compiles and still extends `ConversionException`, so nothing but this catches it.
 *
 * @implements Rule<InClassMethodNode>
 */
final class ExceptionFamilyByConversionMethodRule implements Rule
{
    /**
     * @var string
     */
    private const TYPES = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\';

    /**
     * @var string
     */
    private const FAMILY_PATTERN = '/^Invalid\w+For(PHP|Database)Exception\z/';

    /**
     * @var array<string, string>
     */
    private const FAMILY_BY_CONVERSION_METHOD = [
        'convertToPHPValue' => 'PHP',
        'transformArrayItemForPHP' => 'PHP',
        'convertToDatabaseValue' => 'Database',
        'transformArrayItemForPostgres' => 'Database',
    ];

    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @return array<int, IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classMethod = $node->getOriginalNode();
        if (!$classMethod instanceof ClassMethod) {
            return [];
        }

        $methodName = $classMethod->name->toString();
        $expectedFamily = self::FAMILY_BY_CONVERSION_METHOD[$methodName] ?? null;
        if ($expectedFamily === null) {
            return [];
        }

        $className = $node->getClassReflection()->getName();
        if (!\str_starts_with($className, self::TYPES)) {
            return [];
        }

        $errors = [];
        foreach ($this->raisedExceptionNames($classMethod) as $raised) {
            if (\preg_match(self::FAMILY_PATTERN, $raised, $matches) !== 1) {
                continue;
            }

            if ($matches[1] === $expectedFamily) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(\sprintf(
                '%s::%s() raises %s. It converts %s a database value, so it owes the For%sException family.',
                $className,
                $methodName,
                $raised,
                $expectedFamily === 'PHP' ? 'from' : 'to',
                $expectedFamily
            ))->identifier('martinGeorgiev.dbalType.conversionExceptionFamily')->build();
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    private function raisedExceptionNames(ClassMethod $classMethod): array
    {
        $names = [];
        /** @var array<int, Node\Expr\New_|Node\Expr\StaticCall> $constructions */
        $constructions = (new NodeFinder())->find(
            $classMethod,
            static fn (Node $found): bool => $found instanceof Node\Expr\StaticCall || $found instanceof Node\Expr\New_
        );

        foreach ($constructions as $construction) {
            $raisedClass = $construction->class;
            if (!$raisedClass instanceof Node\Name) {
                continue;
            }

            $names[] = $raisedClass->getLast();
        }

        return $names;
    }
}
