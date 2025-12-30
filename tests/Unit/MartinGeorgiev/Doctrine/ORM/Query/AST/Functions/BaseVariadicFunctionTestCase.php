<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use PHPUnit\Framework\Attributes\Test;

abstract class BaseVariadicFunctionTestCase extends TestCase
{
    abstract protected function createFixture(): BaseVariadicFunction;

    #[Test]
    public function throws_an_exception_when_lexer_is_not_populated_with_a_lookahead_type(): void
    {
        $this->expectException(ParserException::class);

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(new Configuration());

        $query = new Query($em);
        $query->setDQL('SELECT 1');

        $parser = new Parser($query);
        $parser->getLexer()->moveNext();

        $baseVariadicFunction = $this->createFixture();

        $reflectionMethod = new \ReflectionMethod($baseVariadicFunction::class, 'feedParserWithNodesForNodeMappingPattern');
        $reflectionMethod->invoke($baseVariadicFunction, $parser, 'ArithmeticPrimary');
    }

    #[Test]
    public function throws_exception_when_argument_count_is_too_low(): void
    {
        $function = new class('TEST') extends BaseVariadicFunction {
            protected function getFunctionName(): string
            {
                return 'TEST';
            }

            protected function getNodeMappingPattern(): array
            {
                return ['StringPrimary'];
            }

            protected function getMinArgumentCount(): int
            {
                return 2;
            }

            protected function getMaxArgumentCount(): int
            {
                return 3;
            }
        };

        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('TEST() requires at least 2 arguments');

        $reflectionClass = new \ReflectionClass($function);
        $reflectionMethod = $reflectionClass->getMethod('validateArguments');

        $node = $this->createMock(Node::class);
        $reflectionMethod->invoke($function, $node);
    }

    #[Test]
    public function throws_exception_when_argument_count_is_too_high(): void
    {
        $function = new class('TEST') extends BaseVariadicFunction {
            protected function getFunctionName(): string
            {
                return 'TEST';
            }

            protected function getNodeMappingPattern(): array
            {
                return ['StringPrimary'];
            }

            protected function getMinArgumentCount(): int
            {
                return 1;
            }

            protected function getMaxArgumentCount(): int
            {
                return 2;
            }
        };

        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('TEST() requires between 1 and 2 arguments');

        $reflectionClass = new \ReflectionClass($function);
        $reflectionMethod = $reflectionClass->getMethod('validateArguments');

        $node = $this->createMock(Node::class);
        $reflectionMethod->invoke($function, $node, $node, $node);
    }

    #[Test]
    public function throws_exception_when_exact_count_is_required(): void
    {
        $function = new class('TEST') extends BaseVariadicFunction {
            protected function getFunctionName(): string
            {
                return 'TEST';
            }

            protected function getNodeMappingPattern(): array
            {
                return ['StringPrimary'];
            }

            protected function getMinArgumentCount(): int
            {
                return 2;
            }

            protected function getMaxArgumentCount(): int
            {
                return 2;
            }
        };

        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('TEST() requires exactly 2 arguments');

        $reflectionClass = new \ReflectionClass($function);
        $reflectionMethod = $reflectionClass->getMethod('validateArguments');

        $node = $this->createMock(Node::class);
        $reflectionMethod->invoke($function, $node);
    }

    #[Test]
    public function throws_exception_with_singular_argument_for_exact_count(): void
    {
        $function = new class('TEST') extends BaseVariadicFunction {
            protected function getFunctionName(): string
            {
                return 'TEST';
            }

            protected function getNodeMappingPattern(): array
            {
                return ['StringPrimary'];
            }

            protected function getMinArgumentCount(): int
            {
                return 1;
            }

            protected function getMaxArgumentCount(): int
            {
                return 1;
            }
        };

        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('TEST() requires exactly 1 argument');

        $reflectionClass = new \ReflectionClass($function);
        $reflectionMethod = $reflectionClass->getMethod('validateArguments');

        $node = $this->createMock(Node::class);
        $reflectionMethod->invoke($function, $node, $node);
    }

    #[Test]
    public function throws_exception_with_singular_argument_for_at_least(): void
    {
        $function = new class('TEST') extends BaseVariadicFunction {
            protected function getFunctionName(): string
            {
                return 'TEST';
            }

            protected function getNodeMappingPattern(): array
            {
                return ['StringPrimary'];
            }

            protected function getMinArgumentCount(): int
            {
                return 1;
            }

            protected function getMaxArgumentCount(): int
            {
                return 3;
            }
        };

        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('TEST() requires at least 1 argument');

        $reflectionClass = new \ReflectionClass($function);
        $reflectionMethod = $reflectionClass->getMethod('validateArguments');

        $reflectionMethod->invoke($function);
    }
}
