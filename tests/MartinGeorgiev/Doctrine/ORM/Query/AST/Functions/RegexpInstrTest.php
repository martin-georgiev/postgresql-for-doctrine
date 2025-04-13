<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr;

class RegexpInstrTest extends TestCase
{
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
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds position of pattern' => \sprintf("SELECT REGEXP_INSTR(e.text1, 'c(.)(...)') FROM %s e", ContainsTexts::class),
            'finds position of digits' => \sprintf("SELECT REGEXP_INSTR(e.text1, '\\d+') FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_instr() requires between 2 and 6 arguments');

        $dql = \sprintf('SELECT REGEXP_INSTR(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_instr() requires between 2 and 6 arguments');

        $dql = \sprintf("SELECT REGEXP_INSTR(e.text1, 'c(.)(..)', '1', '1', '0', 'i', 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
