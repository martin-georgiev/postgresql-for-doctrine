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

    public function test_from_string(): void
    {
        $ltree = Ltree::fromString('x.y.z');
        self::assertSame(['x', 'y', 'z'], $ltree->getBranch());
        self::assertSame('x.y.z', (string) $ltree);
    }

    public function test_from_string_empty(): void
    {
        $ltree = Ltree::fromString('');
        self::assertSame([], $ltree->getBranch());
        self::assertSame('', (string) $ltree);
    }

    public function test_create_leaf(): void
    {
        $ltree = new Ltree(['root']);
        $newLtree = $ltree->createLeaf('leaf');
        self::assertSame(['root', 'leaf'], $newLtree->getBranch());
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
        $descendant = new Ltree(['a', 'b', 'c']);
        self::assertTrue($ancestor->isAncestorOf($descendant));
        self::assertFalse($descendant->isAncestorOf($ancestor));
    }

    public function test_is_descendant_of(): void
    {
        $ancestor = new Ltree(['a', 'b']);
        $descendant = new Ltree(['a', 'b', 'c']);
        self::assertTrue($descendant->isDescendantOf($ancestor));
        self::assertFalse($ancestor->isDescendantOf($descendant));
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
        self::assertSame(['a', 'b'], $parent->getBranch());
        self::assertSame('a.b', (string) $parent);
    }

    public function test_get_parent_on_root(): void
    {
        $ltree = new Ltree(['a']);
        $parent = $ltree->getParent();
        self::assertSame([], $parent->getBranch());
    }

    public function test_get_parent_throws_on_empty(): void
    {
        $this->expectException(\LogicException::class);
        (new Ltree([]))->getParent();
    }

    public function test_create_leaf_empty_throws(): void
    {
        $ltree = new Ltree(['a']);
        $this->expectException(\InvalidArgumentException::class);
        // @phpstan-ignore-next-line argument.type - Testing invalid type handling
        $ltree->createLeaf('');
    }

    public function test_create_leaf_with_dot_throws(): void
    {
        $ltree = new Ltree(['a']);
        $this->expectException(\InvalidArgumentException::class);
        $ltree->createLeaf('foo.bar');
    }
}
