<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLtreeException;

/**
 * @phpstan-consistent-constructor
 */
class Ltree implements \Stringable, \JsonSerializable
{
    /**
     * @param list<non-empty-string> $pathFromRoot A list with one element represents the root. The list may be empty.
     *
     * @throws InvalidLtreeException if the pathFromRoot is not a valid ltree path
     *                               (contains labels which are empty or contains one or more dots)
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

    /**
     * @throws InvalidLtreeException if $ltree contains invalid/empty labels (e.g., consecutive dots)
     */
    public static function fromString(string $ltree): static
    {
        if ('' === $ltree) {
            return new static([]);
        }

        $pathFromRoot = \explode('.', $ltree);

        return new static($pathFromRoot);
    }

    /**
     * @return list<non-empty-string>
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->getPathFromRoot();
    }

    /**
     * @return list<non-empty-string>
     */
    public function getPathFromRoot(): array
    {
        return $this->pathFromRoot;
    }

    /**
     * @throws \LogicException if the ltree is empty
     */
    public function getParent(): static
    {
        if ($this->isEmpty()) {
            throw new \LogicException('Empty ltree has no parent.');
        }

        $parentPathFromRoot = \array_slice($this->pathFromRoot, 0, -1);

        return new static($parentPathFromRoot);
    }

    public function isEmpty(): bool
    {
        return [] === $this->pathFromRoot;
    }

    /**
     * Checks if the ltree has only one node.
     */
    public function isRoot(): bool
    {
        return 1 === \count($this->pathFromRoot);
    }

    public function isAncestorOf(Ltree $ltree): bool
    {
        if ($this->getPathFromRoot() === $ltree->getPathFromRoot() || $ltree->isEmpty()) {
            return false;
        }

        if ($this->isEmpty()) {
            return true;
        }

        $prefix = \sprintf('%s.', (string) $this);

        return \str_starts_with((string) $ltree, $prefix);
    }

    public function isDescendantOf(Ltree $ltree): bool
    {
        if ($this->getPathFromRoot() === $ltree->getPathFromRoot() || $this->isEmpty()) {
            return false;
        }

        if ($ltree->isEmpty()) {
            return true;
        }

        $prefix = \sprintf('%s.', (string) $ltree);

        return \str_starts_with((string) $this, $prefix);
    }

    public function isParentOf(Ltree $ltree): bool
    {
        if ($ltree->isEmpty()) {
            return false;
        }

        return $this->getPathFromRoot() === $ltree->getParent()->getPathFromRoot();
    }

    public function isChildOf(Ltree $ltree): bool
    {
        if ($this->getPathFromRoot() === $ltree->getPathFromRoot() || $this->isEmpty()) {
            return false;
        }

        return $ltree->getPathFromRoot() === $this->getParent()->getPathFromRoot();
    }

    public function isSiblingOf(Ltree $ltree): bool
    {
        if ($this->isEmpty() || $ltree->isEmpty() || $this->getPathFromRoot() === $ltree->getPathFromRoot()) {
            return false;
        }

        return $this->getParent()->getPathFromRoot() === $ltree->getParent()->getPathFromRoot();
    }

    /**
     * Creates a new Ltree instance with the given leaf added to the end of the path.
     *
     * @param non-empty-string $leaf
     *
     * @throws InvalidLtreeException if the leaf format is invalid (empty string, contains dots, ...)
     */
    public function withLeaf(string $leaf): static
    {
        self::assertValidLtreeNode($leaf);

        $newBranch = [...$this->pathFromRoot, $leaf];

        return new static($newBranch);
    }

    /**
     * @param mixed[] $value
     *
     * @throws InvalidLtreeException if the value is not a list of non-empty strings
     *
     * @phpstan-assert list<non-empty-string> $value
     */
    protected static function assertListOfValidLtreeNodes(array $value): void
    {
        if (!\array_is_list($value)) {
            throw InvalidLtreeException::forInvalidPathFromRootFormat($value, 'list of non-empty strings');
        }

        foreach ($value as $node) {
            self::assertValidLtreeNode($node);
        }
    }

    /**
     * @throws InvalidLtreeException if the value is not a non-empty string
     *
     * @phpstan-assert non-empty-string $value
     */
    protected static function assertValidLtreeNode(mixed $value): void
    {
        if (!\is_string($value) || '' === $value) {
            throw InvalidLtreeException::forInvalidNodeFormat($value, 'non-empty string');
        }

        if (\str_contains($value, '.')) {
            throw InvalidLtreeException::forInvalidNodeFormat($value, 'string without dot');
        }
    }
}
