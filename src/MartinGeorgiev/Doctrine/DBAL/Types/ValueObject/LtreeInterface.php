<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

interface LtreeInterface extends \Stringable, \JsonSerializable
{
    /**
     * @param list<non-empty-string> $pathFromRoot A list with one element represents the root. The list may be empty.
     *
     * @throws \InvalidArgumentException if the pathFromRoot is not a valid ltree path
     *                                   (contains labels which are empty or contains one or more dots)
     */
    public function __construct(array $pathFromRoot);

    /**
     * Creates an Ltree instance from a string representation.
     *
     * @throws \InvalidArgumentException if $ltree contains invalid/empty labels (e.g., consecutive dots)
     */
    public static function fromString(string $ltree): static;

    /**
     * @return list<non-empty-string>
     */
    #[\Override]
    public function jsonSerialize(): array;

    /**
     * @return list<non-empty-string>
     */
    public function getPathFromRoot(): array;

    /**
     * @throws \LogicException if the ltree is empty
     */
    public function getParent(): static;

    public function equals(LtreeInterface $ltree): bool;

    /**
     * Checks if the ltree has no nodes.
     */
    public function isEmpty(): bool;

    /**
     * Checks if the ltree has only one node.
     */
    public function isRoot(): bool;

    public function isAncestorOf(LtreeInterface $ltree): bool;

    public function isDescendantOf(LtreeInterface $ltree): bool;

    public function isParentOf(LtreeInterface $ltree): bool;

    public function isChildOf(LtreeInterface $ltree): bool;

    public function isSiblingOf(LtreeInterface $ltree): bool;

    /**
     * Creates a new Ltree instance with the given leaf added to the end of the path.
     *
     * @param non-empty-string $leaf
     *
     * @throws \InvalidArgumentException if the leaf format is invalid (empty string, contains dots, ...)
     */
    public function withLeaf(string $leaf): static;
}
