<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

interface LtreeInterface extends \Stringable
{
    /**
     * @param list<non-empty-string> $branch
     *
     * @throws \InvalidArgumentException if the branch is empty
     */
    public function __construct(array $branch);

    /**
     * @throws \InvalidArgumentException if the ltree is empty
     */
    public static function fromString(string $ltree): static;

    /**
     * @param non-empty-string $leaf
     *
     * @throws \InvalidArgumentException if the leaf is empty or contains dot
     */
    public function createLeaf(string $leaf): static;

    /**
     * @return list<non-empty-string>
     */
    public function getBranch(): array;

    public function equals(LtreeInterface $ltree): bool;

    public function isAncestorOf(LtreeInterface $ltree): bool;

    public function isDescendantOf(LtreeInterface $ltree): bool;

    public function isRoot(): bool;

    /**
     * @tthrows \LogicException if the ltree is root
     */
    public function getParent(): static;
}
