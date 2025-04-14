<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseComparisonFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

abstract class BaseComparisonFunctionTestCase extends TestCase
{
    abstract protected function createFixture(): BaseComparisonFunction;

    /**
     * @test
     */
    public function throws_an_exception_when_lexer_is_not_populated_with_a_lookahead_type(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(new Configuration());

        $query = new Query($em);
        $query->setDQL('TRUE');

        $parser = new Parser($query);
        $parser->getLexer()->moveNext();

        $baseComparisonFunction = $this->createFixture();

        $reflectionMethod = new \ReflectionMethod($baseComparisonFunction::class, 'feedParserWithNodes');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($baseComparisonFunction, $parser);
    }
}
