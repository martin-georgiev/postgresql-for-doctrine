<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * @phpstan-consistent-constructor
 */
class Ltree implements LtreeInterface
{
    /**
     * @param list<non-empty-string> $branch
     *
     * @throws \InvalidArgumentException if the branch contains empty strings or is not a list
     */
    public function __construct(
        private readonly array $branch,
    ) {
        self::assertListOfNonEmptyStrings($branch);
    }

    #[\Override]
    public function __toString(): string
    {
        return \implode('.', $this->branch);
    }

    #[\Override]
    public static function fromString(string $ltree): static
    {
        if ('' === $ltree) {
            return new static([]);
        }

        $branch = \explode('.', $ltree);

        return new static($branch); // @phpstan-ignore-line argument.type
    }

    #[\Override]
    public function createLeaf(string $leaf): static
    {
        if ('' === $leaf) {
            throw new \InvalidArgumentException('Leaf cannot be empty.');
        }

        if (\str_contains($leaf, '.')) {
            throw new \InvalidArgumentException('Leaf cannot contain dot.');
        }

        $newBranch = [...$this->branch, $leaf];

        return new static($newBranch);
    }

    #[\Override]
    public function getBranch(): array
    {
        return $this->branch;
    }

    #[\Override]
    public function equals(LtreeInterface $ltree): bool
    {
        return $this->branch === $ltree->getBranch();
    }

    #[\Override]
    public function isAncestorOf(LtreeInterface $ltree): bool
    {
        return [] === $this->branch || \str_starts_with((string) $ltree, \sprintf('%s.', (string) $this));
    }

    #[\Override]
    public function isDescendantOf(LtreeInterface $ltree): bool
    {
        return \str_starts_with((string) $this, \sprintf('%s.', (string) $ltree));
    }

    #[\Override]
    public function isRoot(): bool
    {
        return 1 >= \count($this->branch);
    }

    #[\Override]
    public function getParent(): static
    {
        if ([] === $this->branch) {
            throw new \LogicException('Empty ltree has no parent.');
        }

        $parentBranch = \array_slice($this->branch, 0, -1);
        self::assertListOfNonEmptyStrings($parentBranch);

        return new static($parentBranch);
    }

    /**
     * @param mixed[] $value
     *
     * @throws \InvalidArgumentException if the value is not a list of non-empty strings
     *
     * @phpstan-assert list<non-empty-string> $value
     */
    protected static function assertListOfNonEmptyStrings(array $value): void
    {
        if (!\array_is_list($value)) {
            throw new \InvalidArgumentException('Branch must be a list of non-empty strings.');
        }

        \array_map(
            self::assertNonEmptyString(...),
            $value,
        );
    }

    /**
     * @throws \InvalidArgumentException if the value is not a non-empty string
     *
     * @phpstan-assert non-empty-string $value
     */
    protected static function assertNonEmptyString(mixed $value): void
    {
        if (!\is_string($value) || '' === $value) {
            throw new \InvalidArgumentException('Value must be a non-empty string.');
        }
    }
}
