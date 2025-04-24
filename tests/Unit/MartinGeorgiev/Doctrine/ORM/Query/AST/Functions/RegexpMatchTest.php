<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch;

class RegexpMatchTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new RegexpMatch('REGEXP_MATCH');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_MATCH' => RegexpMatch::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic match' => "SELECT regexp_match(c0_.text1, 'pattern') AS sclr_0 FROM ContainsTexts c0_",
            'with flags' => "SELECT regexp_match(c0_.text1, 'pattern', 'i') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic match' => \sprintf("SELECT REGEXP_MATCH(e.text1, 'pattern') FROM %s e", ContainsTexts::class),
            'with flags' => \sprintf("SELECT REGEXP_MATCH(e.text1, 'pattern', 'i') FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_match() requires at least 2 arguments');

        $dql = \sprintf('SELECT REGEXP_MATCH(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_match() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT REGEXP_MATCH(e.text1, 'pattern', 'i', 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
