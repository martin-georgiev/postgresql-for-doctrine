<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseComparisonFunction;

abstract class BaseComparisonFunctionTestCase extends TestCase
{
    abstract protected function createFixture(): BaseComparisonFunction;

    /**
     * @test
     */
    public function throws_an_exception_when_lexer_is_not_populated_with_a_lookahead_type(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The parser\'s "lookahead" property is not populated with a type');

        $lexer = $this->createMock(Lexer::class);
        $lexer->lookahead = null;

        $parser = $this->createMock(Parser::class);
        $parser
            ->expects($this->once())
            ->method('getLexer')
            ->willReturn($lexer);

        $this->createFixture()->feedParserWithNodes($parser);
    }
}
