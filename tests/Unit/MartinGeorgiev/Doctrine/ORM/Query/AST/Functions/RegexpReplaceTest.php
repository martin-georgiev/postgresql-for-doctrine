<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace;

class RegexpReplaceTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new RegexpReplace('REGEXP_REPLACE');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_REPLACE' => RegexpReplace::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic replacement' => "SELECT regexp_replace(c0_.text1, 'pattern', 'replacement') AS sclr_0 FROM ContainsTexts c0_",
            'with flags but no start position' => "SELECT regexp_replace(c0_.text1, 'pattern', 'replacement', 'i') AS sclr_0 FROM ContainsTexts c0_",
            'with start position' => "SELECT regexp_replace(c0_.text1, 'pattern', 'replacement', 3) AS sclr_0 FROM ContainsTexts c0_",
            'with occurrence count but no flags' => "SELECT regexp_replace(c0_.text1, 'pattern', 'replacement', 3, 2) AS sclr_0 FROM ContainsTexts c0_",
            'with occurrence count and flags' => "SELECT regexp_replace(c0_.text1, 'pattern', 'replacement', 3, 2, 'i') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic replacement' => \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement') FROM %s e", ContainsTexts::class),
            'with flags but no start position' => \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 'i') FROM %s e", ContainsTexts::class),
            'with start position' => \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 3) FROM %s e", ContainsTexts::class),
            'with occurrence count but no flags' => \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 3, 2) FROM %s e", ContainsTexts::class),
            'with occurrence count and flags' => \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 3, 2, 'i') FROM %s e", ContainsTexts::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_replace() requires at least 3 arguments');

        $dql = \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('regexp_replace() requires between 3 and 6 arguments');

        $dql = \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 3, 2, 'i', 'extra_arg') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
