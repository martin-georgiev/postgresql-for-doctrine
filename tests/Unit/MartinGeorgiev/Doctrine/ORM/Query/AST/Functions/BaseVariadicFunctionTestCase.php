<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;

abstract class BaseVariadicFunctionTestCase extends TestCase
{
    abstract protected function createFixture(): BaseVariadicFunction;

    /**
     * @test
     */
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
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($baseVariadicFunction, $parser, 'ArithmeticPrimary');
    }
}
