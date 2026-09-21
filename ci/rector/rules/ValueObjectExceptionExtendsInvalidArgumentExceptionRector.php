<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * The DBAL types translate value object failures with catch (\InvalidArgumentException).
 * A value object exception that cannot be caught that way escapes the translation and
 * surfaces from the wrong layer — and no type-level test notices, because the types that
 * catch the concrete class keep working.
 */
final class ValueObjectExceptionExtendsInvalidArgumentExceptionRector extends AbstractRector
{
    private const NAMESPACE_PREFIX = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\ValueObject\\Exceptions\\';

    private const REQUIRED_PARENT = 'InvalidArgumentException';

    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Value object exceptions are catchable as \InvalidArgumentException, which is what the DBAL types catch, and final',
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
        if (!$node instanceof Class_ || $node->isAnonymous()) {
            return null;
        }

        $className = (string) $node->namespacedName;
        if (!\str_starts_with($className, self::NAMESPACE_PREFIX)) {
            return null;
        }

        $hasChanged = false;

        if (!$this->isCatchableAsInvalidArgument($className)) {
            $node->extends = new FullyQualified(self::REQUIRED_PARENT);
            $hasChanged = true;
        }

        // an abstract base in this namespace still owes the parent, but cannot be final
        if (!$node->isAbstract() && !$node->isFinal()) {
            $node->flags |= Modifiers::FINAL;
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    private function isCatchableAsInvalidArgument(string $className): bool
    {
        if (!$this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass(self::REQUIRED_PARENT);

        return $this->reflectionProvider->getClass($className)->isSubclassOfClass($classReflection);
    }
}
