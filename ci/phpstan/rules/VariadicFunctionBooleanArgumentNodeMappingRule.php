<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBooleanLastArgument;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A boolean argument of a variadic DQL function has to be read by StringPrimary.
 *
 * DQL has no boolean literal, so callers spell the value as the string literal 'true' or 'false'.
 * ArithmeticPrimary and SimpleArithmeticExpression reject a string token outright, which makes the
 * whole overload unreachable — the caller gets a parse error for a query the class claims to accept.
 *
 * Extending BaseVariadicFunctionWithOptionalBooleanLastArgument is what marks the last argument as a
 * boolean: that base validates it through BooleanValidationTrait when the caller passes the maximum
 * number of arguments. Nothing else in a function class distinguishes a boolean argument from any
 * other string, so that base is the only signal this rule can read.
 *
 * @implements Rule<InClassNode>
 */
final class VariadicFunctionBooleanArgumentNodeMappingRule implements Rule
{
    /**
     * @var string
     */
    private const BOOLEAN_CARRYING_NODE_TYPE = 'StringPrimary';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (!$classReflection->is(BaseVariadicFunctionWithOptionalBooleanLastArgument::class)) {
            return [];
        }

        $declaration = VariadicFunctionDeclaration::fromClass($node->getOriginalNode(), $scope);
        if (!$declaration instanceof VariadicFunctionDeclaration) {
            return [];
        }

        $maxArgumentCount = $declaration->getMaxArgumentCount();
        if ($maxArgumentCount === null) {
            return [];
        }

        $errors = [];
        foreach ($declaration->getPatterns() as $pattern) {
            $slots = VariadicFunctionDeclaration::slotsOf($pattern);
            $reusesOneSlotForEveryArgument = \count($slots) === 1;
            $patternCannotReachTheBooleanArgument = !$reusesOneSlotForEveryArgument
                && \count($slots) < $maxArgumentCount;
            if ($patternCannotReachTheBooleanArgument) {
                continue;
            }

            $booleanSlot = $reusesOneSlotForEveryArgument ? $slots[0] : $slots[$maxArgumentCount - 1];
            if ($booleanSlot === self::BOOLEAN_CARRYING_NODE_TYPE) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(\sprintf(
                '%s takes a boolean as argument %d, but its node mapping pattern "%s" reads that argument with %s. DQL spells a boolean as the string literal \'true\' or \'false\', which only %s accepts.',
                $classReflection->getDisplayName(),
                $maxArgumentCount,
                $pattern,
                $booleanSlot,
                self::BOOLEAN_CARRYING_NODE_TYPE
            ))
                ->identifier('martinGeorgiev.variadicFunction.booleanNodeMapping')
                ->line($declaration->getDeclarationLine())
                ->build();
        }

        return $errors;
    }
}
