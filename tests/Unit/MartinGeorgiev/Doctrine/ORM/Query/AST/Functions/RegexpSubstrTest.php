<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr;

class RegexpSubstrTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new RegexpSubstr('REGEXP_SUBSTR');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_SUBSTR' => RegexpSubstr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts pattern' => "SELECT regexp_substr(c0_.text1, 'c(.)(...)') AS sclr_0 FROM ContainsTexts c0_",
            'extracts digits' => "SELECT regexp_substr(c0_.text1, '\\d+') AS sclr_0 FROM ContainsTexts c0_",
            'extracts digits with start and N parameters' => "SELECT regexp_substr(c0_.text1, '\\d+', 1, 4) AS sclr_0 FROM ContainsTexts c0_",
            'extracts digits with start and N parameters and flags' => "SELECT regexp_substr(c0_.text1, '\\d+', 1, 4, 'i') AS sclr_0 FROM ContainsTexts c0_",
            'extracts digits with start, N parameters, flags and subexpr parameter' => "SELECT regexp_substr(c0_.text1, '\\d+', 1, 4, 'i', 3) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts pattern' => \sprintf("SELECT REGEXP_SUBSTR(e.text1, 'c(.)(...)') FROM %s e", ContainsTexts::class),
            'extracts digits' => \sprintf("SELECT REGEXP_SUBSTR(e.text1, '\\d+') FROM %s e", ContainsTexts::class),
            'extracts digits with start and N parameters' => \sprintf("SELECT REGEXP_SUBSTR(e.text1, '\\d+', 1, 4) FROM %s e", ContainsTexts::class),
            'extracts digits with start and N parameters and flags' => \sprintf("SELECT REGEXP_SUBSTR(e.text1, '\\d+', 1, 4, 'i') FROM %s e", ContainsTexts::class),
            'extracts digits with start, N parameters, flags and subexpr parameter' => \sprintf("SELECT REGEXP_SUBSTR(e.text1, '\\d+', 1, 4, 'i', 3) FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_substr() requires at least 2 arguments');

        $dql = \sprintf('SELECT REGEXP_SUBSTR(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_substr() requires between 2 and 6 arguments');

        $dql = \sprintf("SELECT REGEXP_SUBSTR(e.text1, 'c(.)(..)', 1, 1, 'i', 1, 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
