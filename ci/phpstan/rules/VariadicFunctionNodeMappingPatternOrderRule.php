<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Patterns are listed from most arguments to fewest. feedParserWithNodes() keeps the first that parses, and a shorter
 * pattern placed first still parses a longer list - it stops early - so later patterns become unreachable and their
 * extra arguments are dropped silently. Equal lengths are alternatives distinguished by type, so this asks for
 * non-increasing rather than strictly decreasing length.
 *
 * @implements Rule<InClassNode>
 */
final class VariadicFunctionNodeMappingPatternOrderRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (!$classReflection->is(BaseVariadicFunction::class)) {
            return [];
        }

        $declaration = VariadicFunctionDeclaration::fromClass($node->getOriginalNode(), $scope);
        if (!$declaration instanceof VariadicFunctionDeclaration) {
            return [];
        }

        $patterns = $declaration->getPatterns();
        $errors = [];
        $counter = \count($patterns);
        for ($index = 1; $index < $counter; $index++) {
            $precedingPattern = $patterns[$index - 1];
            $pattern = $patterns[$index];
            $precedingArgumentCount = \count(VariadicFunctionDeclaration::slotsOf($precedingPattern));
            $argumentCount = \count(VariadicFunctionDeclaration::slotsOf($pattern));
            if ($argumentCount <= $precedingArgumentCount) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(\sprintf(
                '%s lists the node mapping pattern "%s" (%d arguments) after the shorter "%s" (%d arguments). The parser keeps the first pattern that parses, so the longer form is never reached. Order the patterns from most arguments to fewest.',
                $classReflection->getDisplayName(),
                $pattern,
                $argumentCount,
                $precedingPattern,
                $precedingArgumentCount
            ))
                ->identifier('martinGeorgiev.variadicFunction.nodeMappingPatternOrder')
                ->line($declaration->getDeclarationLine())
                ->build();
        }

        return $errors;
    }
}
