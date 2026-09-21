<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * The DBAL types translate value object failures with catch (\InvalidArgumentException).
 * A value object exception extending anything else escapes that catch and surfaces from
 * the wrong layer — and no type-level test notices, because the types that catch the
 * concrete class keep working.
 */
final class ValueObjectExceptionExtendsInvalidArgumentExceptionRector extends AbstractRector
{
    private const NAMESPACE_PREFIX = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\ValueObject\\Exceptions\\';

    private const REQUIRED_PARENT = 'InvalidArgumentException';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Value object exceptions are final and extend \InvalidArgumentException, which is what the DBAL types catch',
            [
                new CodeSample(
                    'class InvalidBoxException extends ConversionException',
                    'final class InvalidBoxException extends \InvalidArgumentException'
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

        $className = (string) $node->namespacedName;
        if (!\str_starts_with($className, self::NAMESPACE_PREFIX)) {
            return null;
        }

        $extendsTheRequiredParent = $node->extends instanceof Node\Name
            && $this->getName($node->extends) === self::REQUIRED_PARENT;
        if ($extendsTheRequiredParent && $node->isFinal()) {
            return null;
        }

        $node->extends = new FullyQualified(self::REQUIRED_PARENT);
        $node->flags |= Modifiers::FINAL;

        return $node;
    }
}
