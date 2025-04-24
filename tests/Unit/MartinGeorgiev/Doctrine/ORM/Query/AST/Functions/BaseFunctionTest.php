<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;
use PHPUnit\Framework\TestCase;

class BaseFunctionTest extends TestCase
{
    /**
     * @test
     */
    public function get_sql_returns_formatted_function_call(): void
    {
        $function = new class('test_function') extends BaseFunction {
            // @phpstan-ignore-next-line The type for $name is not string in older Doctrine versions
            public function __construct(string $name)
            {
                parent::__construct($name);
                $this->customizeFunction();
            }

            protected function customizeFunction(): void
            {
                $this->setFunctionPrototype('TEST(%s, %s)');
            }

            /**
             * @param list<Node|null> $nodes
             */
            public function setNodes(array $nodes): void
            {
                $this->nodes = $nodes;
            }
        };

        $node1 = $this->createMock(Node::class);
        $node1->expects($this->once())
            ->method('dispatch')
            ->willReturn('arg1');

        $node2 = $this->createMock(Node::class);
        $node2->expects($this->once())
            ->method('dispatch')
            ->willReturn('arg2');

        /** @var list<Node|null> $nodes */
        $nodes = [$node1, $node2];
        $function->setNodes($nodes);

        $sqlWalker = $this->createMock(SqlWalker::class);

        $result = $function->getSql($sqlWalker);
        $this->assertEquals('TEST(arg1, arg2)', $result);
    }

    /**
     * @test
     */
    public function get_sql_handles_null_nodes(): void
    {
        $function = new class('test_function') extends BaseFunction {
            // @phpstan-ignore-next-line The type for $name is not string in older Doctrine versions
            public function __construct(string $name)
            {
                parent::__construct($name);
                $this->customizeFunction();
            }

            protected function customizeFunction(): void
            {
                $this->setFunctionPrototype('TEST(%s)');
            }

            /**
             * @param list<Node|null> $nodes
             */
            public function setNodes(array $nodes): void
            {
                $this->nodes = $nodes;
            }
        };

        /** @var list<Node|null> $nodes */
        $nodes = [null];
        $function->setNodes($nodes);

        $sqlWalker = $this->createMock(SqlWalker::class);

        $result = $function->getSql($sqlWalker);
        $this->assertEquals('TEST(null)', $result);
    }
}
