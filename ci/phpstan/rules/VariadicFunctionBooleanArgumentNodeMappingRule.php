<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBooleanLastArgument;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A boolean argument has to be read by StringPrimary. DQL has no boolean literal, so callers spell it `'true'`, which
 * ArithmeticPrimary rejects outright - making the whole overload unreachable.
 *
 * Extending BaseVariadicFunctionWithOptionalBooleanLastArgument is the only signal that an argument is boolean.
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
