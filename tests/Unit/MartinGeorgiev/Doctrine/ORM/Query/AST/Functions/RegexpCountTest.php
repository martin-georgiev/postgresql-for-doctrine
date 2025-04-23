<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount;

class RegexpCountTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new RegexpCount('REGEXP_COUNT');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_COUNT' => RegexpCount::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'counts digits' => "SELECT regexp_count(c0_.text1, '\\d\\d\\d') AS sclr_0 FROM ContainsTexts c0_",
            'counts words' => "SELECT regexp_count(c0_.text1, '\\w+') AS sclr_0 FROM ContainsTexts c0_",
            'with start position' => "SELECT regexp_count(c0_.text1, '\\d\\d\\d', 1) AS sclr_0 FROM ContainsTexts c0_",
            'with flags' => "SELECT regexp_count(c0_.text1, '\\d\\d\\d', 1, 'i') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'counts digits' => \sprintf("SELECT REGEXP_COUNT(e.text1, '\\d\\d\\d') FROM %s e", ContainsTexts::class),
            'counts words' => \sprintf("SELECT REGEXP_COUNT(e.text1, '\\w+') FROM %s e", ContainsTexts::class),
            'with start position' => \sprintf("SELECT REGEXP_COUNT(e.text1, '\\d\\d\\d', 1) FROM %s e", ContainsTexts::class),
            'with flags' => \sprintf("SELECT REGEXP_COUNT(e.text1, '\\d\\d\\d', 1, 'i') FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_count() requires at least 2 arguments');

        $dql = \sprintf('SELECT REGEXP_COUNT(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_count() requires between 2 and 4 arguments');

        $dql = \sprintf("SELECT REGEXP_COUNT(e.text1, '\\d+', 1, 'i', 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
