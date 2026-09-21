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
 * A function whose arguments all read the same way declares one node mapping pattern, not a list.
 *
 * BaseVariadicFunction::feedParserWithNodesForNodeMappingPattern() treats a pattern naming a single
 * parser method as covering every argument, so ['StringPrimary'] already accepts any arity. How many
 * arguments are allowed is the job of getMinArgumentCount() and getMaxArgumentCount(), which the
 * parser enforces separately.
 *
 * Spelling the repetition out instead adds no parsing behaviour and costs some: two or more patterns
 * send the parser through resolvePatternByTokenAnalysis(), which can only ever fail to pick between
 * patterns that are indistinguishable by token type, and every arity then needs its own entry that
 * can fall out of step with the declared minimum and maximum.
 *
 * Multiple patterns are the right shape only when argument positions differ in type.
 *
 * A pattern shorter than getMaxArgumentCount() is not redundant and is left alone: it refuses the
 * arities beyond its own length, which the single-element form would accept, so collapsing it would
 * change what the function parses rather than tidy it.
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
     * Whether the single-element form would parse everything the declared patterns already parse.
     *
     * One pattern naming a single parser method covers any arity; a longer one covers arities up to
     * its own length and makes the parser reject the rest. Collapsing is only a tidy-up when some
     * pattern already reaches the declared maximum.
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
