<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber;
use PHPUnit\Framework\Attributes\Test;

class ToNumberTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_NUMBER' => ToNumber::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts text to number using format pattern' => "SELECT to_number(c0_.text1, '99G999D9S') AS sclr_0 FROM ContainsTexts c0_",
            'converts roman numerals to number' => "SELECT to_number(c0_.text1, 'RN') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts text to number using format pattern' => \sprintf("SELECT TO_NUMBER(e.text1, '99G999D9S') FROM %s e", ContainsTexts::class),
            'converts roman numerals to number' => \sprintf("SELECT TO_NUMBER(e.text1, 'RN') FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT TO_NUMBER(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
