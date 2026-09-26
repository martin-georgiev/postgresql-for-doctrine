<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Arguments that all read the same way declare one pattern, not a list: a pattern naming a single parser method
 * already covers every arity, and min/max enforce the count. Spelling the repetition out sends the parser through
 * resolvePatternByTokenAnalysis(), which cannot pick between patterns indistinguishable by token type.
 *
 * A pattern shorter than getMaxArgumentCount() is left alone - it refuses arities the single-element form accepts, so
 * collapsing it would change what the function parses.
 *
 * @implements Rule<InClassNode>
 */
final class VariadicFunctionHomogeneousNodeMappingRule implements Rule
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
        $slots = [];
        foreach ($patterns as $pattern) {
            foreach (VariadicFunctionDeclaration::slotsOf($pattern) as $slot) {
                $slots[] = $slot;
            }
        }

        $distinctSlots = \array_values(\array_unique($slots));
        if (\count($distinctSlots) !== 1) {
            return [];
        }

        $onlySlot = $distinctSlots[0];
        if ($patterns === [$onlySlot]) {
            return [];
        }

        $maxArgumentCount = $declaration->getMaxArgumentCount();
        if ($maxArgumentCount === null || !$this->acceptsEveryAllowedArity($patterns, $maxArgumentCount)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(\sprintf(
                "%s reads every argument with %s, so its node mapping pattern should be the single-element ['%s'] and the arity left to getMinArgumentCount() and getMaxArgumentCount(). Spelling it out as %s repeats what one entry already covers.",
                $classReflection->getDisplayName(),
                $onlySlot,
                $onlySlot,
                "['".\implode("', '", $patterns)."']"
            ))
                ->identifier('martinGeorgiev.variadicFunction.homogeneousNodeMapping')
                ->line($declaration->getDeclarationLine())
                ->build(),
        ];
    }

    /**
     * Whether collapsing preserves behaviour: only when some pattern already reaches the declared maximum.
     *
     * @param list<string> $patterns
     */
    private function acceptsEveryAllowedArity(array $patterns, int $maxArgumentCount): bool
    {
        foreach ($patterns as $pattern) {
            $slotCount = \count(VariadicFunctionDeclaration::slotsOf($pattern));
            if ($slotCount === 1 || $slotCount >= $maxArgumentCount) {
                return true;
            }
        }

        return false;
    }
}
