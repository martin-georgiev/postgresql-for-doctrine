<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Keeps `@param` and `@throws` on a DBAL type's two conversion methods carrying a type and nothing else.
 * A description there restates the signature. Running prose above the tags is left alone - that is where a
 * non-obvious PostgreSQL reason belongs, as `Lquery::convertToPHPValue()` shows.
 *
 * @implements Rule<InClassMethodNode>
 */
final class ConversionMethodDocBlockRule implements Rule
{
    /**
     * @var string
     */
    private const TYPES = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\';

    /**
     * @var array<int, string>
     */
    private const CONVERSION_METHODS = ['convertToDatabaseValue', 'convertToPHPValue'];

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
        if (!\in_array($methodName, self::CONVERSION_METHODS, true)) {
            return [];
        }

        $className = $node->getClassReflection()->getName();
        if (!\str_starts_with($className, self::TYPES)) {
            return [];
        }

        $errors = [];
        foreach ($this->describedTags($classMethod->getDocComment()?->getText() ?? '') as $tag) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                '%s::%s() describes its @%s. The tag carries the type only - the signature already says the rest.',
                $className,
                $methodName,
                $tag
            ))->identifier('martinGeorgiev.dbalType.describedConversionTag')->build();
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    private function describedTags(string $docBlock): array
    {
        $tags = [];
        foreach (\preg_split('/\R/', $docBlock) ?: [] as $line) {
            $line = \trim((string) \preg_replace('#^\h*(?:/\*\*|\*)\h*#', '', \trim($line)));

            // The variable name closes a `@param`, so whatever trails it is a description rather than the type.
            if (\preg_match('/^@param\h+.*?\$\w+\h*(\S.*)?$/', $line, $matches) === 1 && ($matches[1] ?? '') !== '') {
                $tags[] = 'param';
            }

            if (\preg_match('/^@throws\h+\S+\h+(\S.*)$/', $line) === 1) {
                $tags[] = 'throws';
            }
        }

        return \array_values(\array_unique($tags));
    }
}
