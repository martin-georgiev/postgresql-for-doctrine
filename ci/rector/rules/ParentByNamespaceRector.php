<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Keeps every class under a namespace a subtype of the parent that namespace promises.
 *
 * Configured with a map of namespace prefix to required parent. A class under a
 * configured prefix that is not already a subtype of that parent is repointed at it.
 * A class reaching the parent through an intermediate base already satisfies it and
 * is left alone; abstract classes owe the parent like any other.
 */
final class ParentByNamespaceRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, class-string>
     */
    private array $requiredParentByNamespacePrefix = [];

    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $requiredParentByNamespacePrefix = [];
        foreach ($configuration as $namespacePrefix => $requiredParent) {
            if (!\is_string($namespacePrefix) || $namespacePrefix === '' || !\is_string($requiredParent)) {
                throw new \InvalidArgumentException(\sprintf(
                    '%s takes a map of namespace prefix to parent class name',
                    self::class
                ));
            }

            if (!$this->reflectionProvider->hasClass($requiredParent)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Configured parent %s cannot be found',
                    \var_export($requiredParent, true)
                ));
            }

            $requiredParentByNamespacePrefix[$namespacePrefix] = $requiredParent;
        }

        $this->requiredParentByNamespacePrefix = $requiredParentByNamespacePrefix;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Classes under a configured namespace are a subtype of the parent that namespace promises',
            [
                new ConfiguredCodeSample(
                    'final class InvalidBoxException extends ConversionException',
                    'final class InvalidBoxException extends \InvalidArgumentException',
                    ['Acme\\ValueObject\\Exceptions\\' => \InvalidArgumentException::class]
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
        $requiredParent = $this->matchRequiredParent($className);
        if ($requiredParent === null || $this->isSubtypeOf($className, $requiredParent)) {
            return null;
        }

        $node->extends = new FullyQualified($requiredParent);

        return $node;
    }

    /**
     * @return class-string|null
     */
    private function matchRequiredParent(string $className): ?string
    {
        foreach ($this->requiredParentByNamespacePrefix as $namespacePrefix => $requiredParent) {
            if (\str_starts_with($className, $namespacePrefix)) {
                return $requiredParent;
            }
        }

        return null;
    }

    private function isSubtypeOf(string $className, string $requiredParent): bool
    {
        if (!$this->reflectionProvider->hasClass($className)) {
            return false;
        }

        return $this->reflectionProvider->getClass($className)
            ->isSubclassOfClass($this->reflectionProvider->getClass($requiredParent));
    }
}
