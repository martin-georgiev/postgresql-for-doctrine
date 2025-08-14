<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use PHPUnit\Framework\TestCase;

final class LtreeTest extends TestCase
{
    public function test_construct_and_to_string(): void
    {
        $ltree = new Ltree(['a', 'b', 'c']);
        self::assertSame('a.b.c', (string) $ltree);
    }

    public function test_construct_trows_on_non_list(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Ltree([0 => 'a', 2 => 'b', 3 => 'c']); // @phpstan-ignore argument.type
    }

    public function test_construct_trows_on_empty_string_in_path_from_root(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Ltree(['a', '', 'c']); // @phpstan-ignore argument.type
    }

    public function test_from_string(): void
    {
        $ltree = Ltree::fromString('x.y.z');
        self::assertSame(['x', 'y', 'z'], $ltree->getPathFromRoot());
        self::assertSame('x.y.z', (string) $ltree);
    }

    public function test_from_string_empty(): void
    {
        $ltree = Ltree::fromString('');
        self::assertSame([], $ltree->getPathFromRoot());
        self::assertSame('', (string) $ltree);
    }

    public function test_json_serialize(): void
    {
        $pathFromRoot = ['a', 'b', 'c'];
        $ltree = new Ltree($pathFromRoot);
        self::assertSame($pathFromRoot, $ltree->jsonSerialize());
    }

    public function test_json_encode(): void
    {
        $ltree = new Ltree(['a', 'b', 'c']);
        $json = \json_encode($ltree);
        self::assertSame('["a","b","c"]', $json);
    }

    public function test_with_leaf(): void
    {
        $ltree = new Ltree(['root']);
        $newLtree = $ltree->withLeaf('leaf');
        self::assertSame(['root', 'leaf'], $newLtree->getPathFromRoot());
        self::assertSame('root.leaf', (string) $newLtree);
    }

    public function test_equals(): void
    {
        $a = new Ltree(['foo', 'bar']);
        $b = new Ltree(['foo', 'bar']);
        $c = new Ltree(['foo', 'baz']);
        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }

    public function test_is_ancestor_of(): void
    {
        $ancestor = new Ltree(['a', 'b']);
        $leaf = new Ltree(['a', 'b', 'c']);
        self::assertTrue($ancestor->isAncestorOf($leaf));
        self::assertFalse($leaf->isAncestorOf($ancestor));
    }

    public function test_is_leaf_of(): void
    {
        $ancestor = new Ltree(['a', 'b']);
        $leaf = new Ltree(['a', 'b', 'c']);
        self::assertTrue($leaf->isLeafOf($ancestor));
        self::assertFalse($ancestor->isLeafOf($leaf));
    }

    public function test_is_root(): void
    {
        $emptyRoot = new Ltree([]);
        $root = new Ltree(['a']);
        $notRoot = new Ltree(['a', 'b']);
        self::assertTrue($emptyRoot->isRoot());
        self::assertTrue($root->isRoot());
        self::assertFalse($notRoot->isRoot());
    }

    public function test_get_parent(): void
    {
        $ltree = new Ltree(['a', 'b', 'c']);
        $parent = $ltree->getParent();
        self::assertSame(['a', 'b'], $parent->getPathFromRoot());
        self::assertSame('a.b', (string) $parent);
    }

    public function test_get_parent_respect_immutability(): void
    {
        $ltree = new Ltree(['a', 'b', 'c']);
        $parent = $ltree->getParent();
        self::assertNotSame($ltree, $parent);
        self::assertSame(['a', 'b', 'c'], $ltree->getPathFromRoot());
        self::assertSame('a.b.c', (string) $ltree);
    }

    public function test_get_parent_on_root(): void
    {
        $ltree = new Ltree(['a']);
        $parent = $ltree->getParent();
        self::assertSame([], $parent->getPathFromRoot());
    }

    public function test_get_parent_throws_on_empty(): void
    {
        $this->expectException(\LogicException::class);
        (new Ltree([]))->getParent();
    }

    public function test_with_leaf_empty_throws(): void
    {
        $ltree = new Ltree(['a']);
        $this->expectException(\InvalidArgumentException::class);
        $ltree->withLeaf(''); // @phpstan-ignore argument.type
    }

    public function test_with_leaf_with_dot_throws(): void
    {
        $ltree = new Ltree(['a']);
        $this->expectException(\InvalidArgumentException::class);
        $ltree->withLeaf('foo.bar');
    }
}
