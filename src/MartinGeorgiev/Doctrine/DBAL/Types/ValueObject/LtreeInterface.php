<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

interface LtreeInterface extends \Stringable, \JsonSerializable
{
    /**
     * @param list<non-empty-string> $pathFromRoot
     *
     * @throws \InvalidArgumentException if the pathFromRoot is empty
     */
    public function __construct(array $pathFromRoot);

    /**
     * @throws \InvalidArgumentException if the ltree is empty
     */
    public static function fromString(string $ltree): static;

    /**
     * @return list<non-empty-string>
     */
    #[\Override]
    public function jsonSerialize(): array;

    /**
     * @param non-empty-string $leaf
     *
     * @throws \InvalidArgumentException if the leaf is empty or contains dot
     */
    public function withLeaf(string $leaf): static;

    /**
     * @return list<non-empty-string>
     */
    public function getPathFromRoot(): array;

    public function equals(LtreeInterface $ltree): bool;

    public function isAncestorOf(LtreeInterface $ltree): bool;

    public function isLeafOf(LtreeInterface $ltree): bool;

    public function isRoot(): bool;

    /**
     * @tthrows \LogicException if the ltree is root
     */
    public function getParent(): static;
}
