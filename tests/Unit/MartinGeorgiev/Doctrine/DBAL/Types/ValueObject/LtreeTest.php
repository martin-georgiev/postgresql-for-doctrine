<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LtreeTest extends TestCase
{
    public function test_construct_and_to_string(): void
    {
        $ltree = new Ltree(['a', 'b', 'c']);
        self::assertSame('a.b.c', (string) $ltree);
    }

    /**
     * @param mixed[] $value
     */
    #[DataProvider('badPathFromRootProvider')]
    public function test_construct_throws_on_bad_path_from_root(array $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Ltree($value); // @phpstan-ignore argument.type
    }

    /**
     * @return iterable<string, array<array-key,mixed[]>>
     */
    public static function badPathFromRootProvider(): iterable
    {
        yield 'not a list' => [[0 => 'a', 2 => 'b', 3 => 'c']];
        yield 'empty string in path' => [['a', '', 'c']];
        yield 'list with object' => [['a', new \stdClass(), 'c']];
        yield 'list with null' => [['a', null, 'c']];
        yield 'list with dotted string' => [['a', 'b.c', 'ds']];
    }

    #[DataProvider('badRepresentationProvider')]
    public function test_from_string_throws_on_bad_value(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Ltree::fromString($value);
    }

    /**
     * @return iterable<string, string[]>
     */
    public static function badRepresentationProvider(): iterable
    {
        yield 'string starting with dot' => ['.b'];
        yield 'string ending with dot' => ['a.'];
    }

    /**
     * @param list<non-empty-string> $expected
     */
    #[DataProvider('goodRepresentationProvider')]
    public function test_from_string(string $value, array $expected): void
    {
        $ltree = Ltree::fromString($value);
        self::assertSame($expected, $ltree->getPathFromRoot());
        self::assertSame($value, (string) $ltree);
    }

    /**
     * @param list<non-empty-string> $value
     */
    #[DataProvider('goodRepresentationProvider')]
    public function test_to_string(string $expected, array $value): void
    {
        $ltree = new Ltree($value);
        self::assertSame($expected, (string) $ltree);
    }

    /**
     * @param list<non-empty-string> $expected
     */
    #[DataProvider('goodRepresentationProvider')]
    public function test_json_serialize(string $value, array $expected): void
    {
        $ltreeFromString = Ltree::fromString($value);
        self::assertSame($expected, $ltreeFromString->jsonSerialize());

        $ltree = new Ltree($expected);
        self::assertSame($expected, $ltree->jsonSerialize());
    }

    /**
     * @return iterable<string, array{0: string, 1: list<non-empty-string>}>
     */
    public static function goodRepresentationProvider(): iterable
    {
        yield 'empty string' => ['', []];
        yield 'single node' => ['a', ['a']];
        yield 'multiple nodes' => ['a.b.c', ['a', 'b', 'c']];
        yield 'with numbers' => ['1.2.3', ['1', '2', '3']];
        yield 'with special characters' => ['a.b.c-d_e', ['a', 'b', 'c-d_e']];
    }

    public function test_json_encode(): void
    {
        $ltree = new Ltree(['a', 'b', 'c']);
        $json = \json_encode($ltree);
        self::assertSame('["a","b","c"]', $json);
    }

    #[DataProvider('parentProvider')]
    public function test_get_parent(Ltree $child, Ltree $parent): void
    {
        $ltree = $child->getParent();
        self::assertSame((string) $parent, (string) $ltree);
    }

    #[DataProvider('parentProvider')]
    public function test_get_parent_respect_immutability(Ltree $child, Ltree $parent): void
    {
        unset($parent);
        $childAsString = (string) $child;
        $ltree = $child->getParent();
        self::assertNotSame($child, $ltree, 'getParent() should return a new instance');
        self::assertSame($childAsString, (string) $child, 'getParent() should not mutate the original instance');
    }

    /**
     * @return iterable<string, array{child: Ltree, parent: Ltree}>
     */
    public static function parentProvider(): iterable
    {
        yield 'root' => [
            'child' => new Ltree(['a']),
            'parent' => new Ltree([]),
        ];

        yield 'child with nodes' => [
            'child' => new Ltree(['a', 'b', 'c']),
            'parent' => new Ltree(['a', 'b']),
        ];
    }

    public function test_get_parent_throws_on_empty(): void
    {
        $this->expectException(\LogicException::class);
        (new Ltree([]))->getParent();
    }

    public function test_is_empty(): void
    {
        $ltree = new Ltree([]);
        self::assertTrue($ltree->isEmpty());

        $ltreeWithNodes = new Ltree(['a', 'b']);
        self::assertFalse($ltreeWithNodes->isEmpty());
    }

    public function test_is_root(): void
    {
        $emptyRoot = new Ltree([]);
        $root = new Ltree(['a']);
        $notRoot = new Ltree(['a', 'b']);
        self::assertFalse($emptyRoot->isRoot());
        self::assertTrue($root->isRoot());
        self::assertFalse($notRoot->isRoot());
    }

    /**
     * @param $expected array{
     *                  equals: bool,
     *                  isAncestorOf: bool,
     *                  isDescendantOf: bool,
     *                  isParentOf: bool,
     *                  isChildOf: bool,
     *                  isSiblingOf: bool,
     *                  }
     */
    #[DataProvider('familyProvider')]
    public function test_relationships(
        Ltree $left,
        Ltree $right,
        array $expected,
    ): void {
        foreach ($expected as $method => $value) {
            self::assertSame(
                $value,
                $left->{$method}($right),
                \sprintf('Failed  %s check', $method),
            );
        }
    }

    /**
     * @return iterable<string, array{
     *   left: Ltree,
     *   right: Ltree,
     *   expected: array{
     *     equals: bool,
     *     isAncestorOf: bool,
     *     isDescendantOf: bool,
     *     isParentOf: bool,
     *     isChildOf: bool,
     *     isSiblingOf: bool,
     *   },
     * }>
     */
    public static function familyProvider(): iterable
    {
        $empty = new Ltree([]);
        $root = new Ltree(['a']);
        $child = new Ltree(['a', 'b']);
        $secondChild = new Ltree(['a', 'e']);
        $grandChild = new Ltree(['a', 'b', 'c']);
        $secondGrandChild = new Ltree(['a', 'b', 'd']);
        $thirdGrandChild = new Ltree(['a', 'e', 'f']);
        $unrelated = new Ltree(['x', 'y']);

        yield 'empty is ? of empty' => [
            'left' => $empty,
            'right' => $empty,
            'expected' => [
                'equals' => true,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'empty is ? of root' => [
            'left' => $empty,
            'right' => $root,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => true,
                'isDescendantOf' => false,
                'isParentOf' => true,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'root is ? of empty' => [
            'left' => $root,
            'right' => $empty,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => true,
                'isParentOf' => false,
                'isChildOf' => true,
                'isSiblingOf' => false,
            ],
        ];

        yield 'root is ? of root' => [
            'left' => $root,
            'right' => $root,
            'expected' => [
                'equals' => true,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'child is ? of empty' => [
            'left' => $child,
            'right' => $empty,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => true,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'root is ? of child' => [
            'left' => $root,
            'right' => $child,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => true,
                'isDescendantOf' => false,
                'isParentOf' => true,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'child is ? of root' => [
            'left' => $child,
            'right' => $root,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => true,
                'isParentOf' => false,
                'isChildOf' => true,
                'isSiblingOf' => false,
            ],
        ];

        yield 'child is ? of child' => [
            'left' => $child,
            'right' => $child,
            'expected' => [
                'equals' => true,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'child is ? of grandChild' => [
            'left' => $child,
            'right' => $grandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => true,
                'isDescendantOf' => false,
                'isParentOf' => true,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'grandChild is ? of child' => [
            'left' => $grandChild,
            'right' => $child,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => true,
                'isParentOf' => false,
                'isChildOf' => true,
                'isSiblingOf' => false,
            ],
        ];

        yield 'child is ? of unrelated' => [
            'left' => $child,
            'right' => $unrelated,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'unrelated is ? of child' => [
            'left' => $unrelated,
            'right' => $child,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'child is ? of secondChild' => [
            'left' => $child,
            'right' => $secondChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => true,
            ],
        ];

        yield 'secondChild is ? of child' => [
            'left' => $secondChild,
            'right' => $child,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => true,
            ],
        ];

        yield 'grandChild is ? of secondGrandChild' => [
            'left' => $grandChild,
            'right' => $secondGrandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => true,
            ],
        ];

        yield 'secondGrandChild is ? of grandChild' => [
            'left' => $secondGrandChild,
            'right' => $grandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => true,
            ],
        ];

        yield 'grandChild is ? of thirdGrandChild' => [
            'left' => $grandChild,
            'right' => $thirdGrandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'thirdGrandChild is ? of grandChild' => [
            'left' => $thirdGrandChild,
            'right' => $grandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'secondChild is ? of secondGrandChild' => [
            'left' => $secondChild,
            'right' => $secondGrandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => false,
                'isDescendantOf' => false,
                'isParentOf' => false,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];

        yield 'secondChild is ? of thirdGrandChild' => [
            'left' => $secondChild,
            'right' => $thirdGrandChild,
            'expected' => [
                'equals' => false,
                'isAncestorOf' => true,
                'isDescendantOf' => false,
                'isParentOf' => true,
                'isChildOf' => false,
                'isSiblingOf' => false,
            ],
        ];
    }

    /**
     * @param non-empty-string $leaf
     */
    #[DataProvider('goodLeafProvider')]
    public function test_with_leaf(Ltree $parent, string $leaf, Ltree $expected): void
    {
        $ltree = $parent->withLeaf($leaf);
        self::assertSame((string) $expected, (string) $ltree);
    }

    /**
     * @param non-empty-string $leaf
     */
    #[DataProvider('goodLeafProvider')]
    public function test_with_leaf_respects_immutability(Ltree $parent, string $leaf, Ltree $expected): void
    {
        unset($expected);

        $parentAsString = (string) $parent;
        $ltree = $parent->withLeaf($leaf);
        self::assertNotSame($parent, $ltree, 'withLeaf() should return a new instance');
        self::assertSame($parentAsString, (string) $parent, 'withLeaf() should not mutate the original instance');
    }

    /**
     * @return iterable<string, array{0: Ltree, 1: non-empty-string, 2: Ltree}>
     */
    public static function goodLeafProvider(): iterable
    {
        yield 'add leaf to empty' => [new Ltree([]), 'a', new Ltree(['a'])];

        yield 'add leaf to root' => [new Ltree(['a']), 'b', new Ltree(['a', 'b'])];

        yield 'add leaf to child' => [new Ltree(['a', 'b']), 'c', new Ltree(['a', 'b', 'c'])];
    }

    #[DataProvider('badLeafProvider')]
    public function test_with_leaf_throws(string $leaf): void
    {
        $ltree = new Ltree(['a', 'b']);
        $this->expectException(\InvalidArgumentException::class);
        $ltree->withLeaf($leaf); // @phpstan-ignore argument.type
    }

    /**
     * @return iterable<string, string[]>
     */
    public static function badLeafProvider(): iterable
    {
        yield 'with empty leaf' => [''];
        yield 'with leaf with dot' => ['a.b'];
        yield 'with leaf starting by dot' => ['.b'];
        yield 'with leaf ending by dot' => ['a.'];
    }
}
