<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr;

class RegexpInstrTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new RegexpInstr('REGEXP_INSTR');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_INSTR' => RegexpInstr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'finds position of pattern' => "SELECT regexp_instr(c0_.text1, 'c(.)(...)') AS sclr_0 FROM ContainsTexts c0_",
            'finds position of digits' => "SELECT regexp_instr(c0_.text1, '\\d+') AS sclr_0 FROM ContainsTexts c0_",
            'with start position' => "SELECT regexp_instr(c0_.text1, '\\d+', 1) AS sclr_0 FROM ContainsTexts c0_",
            'with start and occurrence' => "SELECT regexp_instr(c0_.text1, '\\d+', 1, 2) AS sclr_0 FROM ContainsTexts c0_",
            'with start, occurrence and return_option' => "SELECT regexp_instr(c0_.text1, '\\d+', 1, 2, 0) AS sclr_0 FROM ContainsTexts c0_",
            'with flags' => "SELECT regexp_instr(c0_.text1, '\\d+', 1, 2, 0, 'i') AS sclr_0 FROM ContainsTexts c0_",
            'with subexpr' => "SELECT regexp_instr(c0_.text1, '\\d+', 1, 2, 0, 'i', 2) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds position of pattern' => \sprintf("SELECT REGEXP_INSTR(e.text1, 'c(.)(...)') FROM %s e", ContainsTexts::class),
            'finds position of digits' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+') FROM %s e", ContainsTexts::class),
            'with start position' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+', 1) FROM %s e", ContainsTexts::class),
            'with start and occurrence' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+', 1, 2) FROM %s e", ContainsTexts::class),
            'with start, occurrence and return_option' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+', 1, 2, 0) FROM %s e", ContainsTexts::class),
            'with flags' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+', 1, 2, 0, 'i') FROM %s e", ContainsTexts::class),
            'with subexpr' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+', 1, 2, 0, 'i', 2) FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_instr() requires at least 2 arguments');

        $dql = \sprintf('SELECT REGEXP_INSTR(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_instr() requires between 2 and 7 arguments');

        $dql = \sprintf("SELECT REGEXP_INSTR(e.text1, 'c(.)(..)', 1, 1, 0, 'i', 2, 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
