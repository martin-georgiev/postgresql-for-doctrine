<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Keeps a set of methods declared in the relative order the configuration names them.
 *
 * Configured with an ordered list of method names. A class declaring two or more of
 * them out of that order has them swapped back into it, each taking a slot one of the
 * others already occupied. Methods outside the list never move, so the reordering is
 * confined to the named ones and the rest of the class body is left as written.
 */
final class MethodOrderRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * Method names are case-insensitive in PHP, so ranks are keyed by the lowercased name.
     *
     * @var array<string, int>
     */
    private array $rankByMethodName = [];

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $rankByMethodName = [];
        foreach (\array_values($configuration) as $rank => $methodName) {
            if (!\is_string($methodName) || $methodName === '') {
                throw new \InvalidArgumentException(\sprintf(
                    '%s takes an ordered list of method names',
                    self::class
                ));
            }

            $lowercasedMethodName = \strtolower($methodName);
            if (\array_key_exists($lowercasedMethodName, $rankByMethodName)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Method %s is named more than once and cannot be given an order',
                    \var_export($methodName, true)
                ));
            }

            $rankByMethodName[$lowercasedMethodName] = $rank;
        }

        if (\count($rankByMethodName) < 2) {
            throw new \InvalidArgumentException(\sprintf(
                '%s needs at least two method names to order them against each other',
                self::class
            ));
        }

        $this->rankByMethodName = $rankByMethodName;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Methods named in the configuration are declared in the relative order it gives them',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
                        final class Bit
                        {
                            public function convertToPHPValue($value): ?string
                            {
                            }

                            public function convertToDatabaseValue($value): ?string
                            {
                            }
                        }
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        final class Bit
                        {
                            public function convertToDatabaseValue($value): ?string
                            {
                            }

                            public function convertToPHPValue($value): ?string
                            {
                            }
                        }
                        CODE_SAMPLE,
                    ['convertToDatabaseValue', 'convertToPHPValue']
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $rankByOccupiedSlot = $this->findRankByOccupiedSlot($node);
        if (\count($rankByOccupiedSlot) < 2) {
            return null;
        }

        $ranksInDeclarationOrder = \array_values($rankByOccupiedSlot);
        $ranksInRequiredOrder = $ranksInDeclarationOrder;
        \sort($ranksInRequiredOrder);
        if ($ranksInDeclarationOrder === $ranksInRequiredOrder) {
            return null;
        }

        $methodByRank = [];
        foreach ($rankByOccupiedSlot as $slot => $rank) {
            $methodByRank[$rank] = $node->stmts[$slot];
        }

        \ksort($methodByRank);

        $occupiedSlots = \array_keys($rankByOccupiedSlot);
        foreach (\array_values($methodByRank) as $position => $method) {
            $node->stmts[$occupiedSlots[$position]] = $method;
        }

        return $node;
    }

    /**
     * @return array<array-key, int> the slot each configured method occupies, in declaration order, mapped to its configured rank
     */
    private function findRankByOccupiedSlot(Class_ $class): array
    {
        $rankByOccupiedSlot = [];
        foreach ($class->stmts as $slot => $stmt) {
            if (!$stmt instanceof ClassMethod) {
                continue;
            }

            $methodName = \strtolower($this->getName($stmt) ?? '');
            if (!\array_key_exists($methodName, $this->rankByMethodName)) {
                continue;
            }

            $rankByOccupiedSlot[$slot] = $this->rankByMethodName[$methodName];
        }

        return $rankByOccupiedSlot;
    }
}
