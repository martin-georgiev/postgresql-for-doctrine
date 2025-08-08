<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * @phpstan-consistent-constructor
 */
class Ltree implements LtreeInterface
{
    /**
     * @param list<non-empty-string> $pathFromRoot
     *
     * @throws \InvalidArgumentException if the pathFromRoot contains empty strings or is not a list
     */
    public function __construct(
        private readonly array $pathFromRoot,
    ) {
        self::assertListOfValidLtreeNodes($pathFromRoot);
    }

    #[\Override]
    public function __toString(): string
    {
        return \implode('.', $this->pathFromRoot);
    }

    #[\Override]
    public static function fromString(string $ltree): static
    {
        if ('' === $ltree) {
            return new static([]);
        }

        $pathFromRoot = \explode('.', $ltree);

        self::assertListOfValidLtreeNodes($pathFromRoot);

        return new static($pathFromRoot);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->pathFromRoot;
    }

    #[\Override]
    public function getPathFromRoot(): array
    {
        return $this->pathFromRoot;
    }

    #[\Override]
    public function getParent(): static
    {
        if ($this->isEmpty()) {
            throw new \LogicException('Empty ltree has no parent.');
        }

        $parentPathFromRoot = \array_slice($this->pathFromRoot, 0, -1);

        return new static($parentPathFromRoot);
    }

    #[\Override]
    public function equals(LtreeInterface $ltree): bool
    {
        return $this->pathFromRoot === $ltree->getPathFromRoot();
    }

    #[\Override]
    public function isEmpty(): bool
    {
        return [] === $this->pathFromRoot;
    }

    #[\Override]
    public function isRoot(): bool
    {
        return 1 === \count($this->pathFromRoot);
    }

    #[\Override]
    public function isAncestorOf(LtreeInterface $ltree): bool
    {
        if ($this->equals($ltree) || $ltree->isEmpty()) {
            return false;
        }

        if ($this->isEmpty()) {
            return true;
        }

        $prefix = \sprintf('%s.', (string) $this);

        return \str_starts_with((string) $ltree, $prefix);
    }

    #[\Override]
    public function isDescendantOf(LtreeInterface $ltree): bool
    {
        if ($this->equals($ltree) || $this->isEmpty()) {
            return false;
        }

        if ($ltree->isEmpty()) {
            return true;
        }

        $prefix = \sprintf('%s.', (string) $ltree);

        return \str_starts_with((string) $this, $prefix);
    }

    #[\Override]
    public function isParentOf(LtreeInterface $ltree): bool
    {
        if ($ltree->isEmpty()) {
            return false;
        }

        return $this->equals($ltree->getParent());
    }

    #[\Override]
    public function isChildOf(LtreeInterface $ltree): bool
    {
        if ($this->equals($ltree) || $this->isEmpty()) {
            return false;
        }

        return $ltree->equals($this->getParent());
    }

    #[\Override]
    public function isSiblingOf(LtreeInterface $ltree): bool
    {
        if ($this->isEmpty() || $ltree->isEmpty() || $this->equals($ltree)) {
            return false;
        }

        return $this->getParent()->equals($ltree->getParent());
    }

    #[\Override]
    public function withLeaf(string $leaf): static
    {
        self::assertValidLtreeNode($leaf);

        $newBranch = [...$this->pathFromRoot, $leaf];

        return new static($newBranch);
    }

    /**
     * @param mixed[] $value
     *
     * @throws \InvalidArgumentException if the value is not a list of non-empty strings
     *
     * @phpstan-assert list<non-empty-string> $value
     */
    protected static function assertListOfValidLtreeNodes(array $value): void
    {
        if (!\array_is_list($value)) {
            throw new \InvalidArgumentException('Branch must be a list of non-empty strings.');
        }

        foreach ($value as $node) {
            self::assertValidLtreeNode($node);
        }
    }

    /**
     * @throws \InvalidArgumentException if the value is not a non-empty string
     *
     * @phpstan-assert non-empty-string $value
     */
    protected static function assertValidLtreeNode(mixed $value): void
    {
        if (!\is_string($value) || '' === $value) {
            throw new \InvalidArgumentException('Value must be a non-empty string.');
        }

        if (\str_contains($value, '.')) {
            throw new \InvalidArgumentException('Value cannot contain dot.');
        }
    }
}
