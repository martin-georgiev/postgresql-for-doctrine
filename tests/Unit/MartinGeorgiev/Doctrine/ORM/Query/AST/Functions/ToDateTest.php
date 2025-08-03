<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate;
use PHPUnit\Framework\Attributes\Test;

class ToDateTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_DATE' => ToDate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts text to date using format pattern' => "SELECT to_date(c0_.text1, 'DD Mon YYYY') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts text to date using format pattern' => \sprintf("SELECT TO_DATE(e.text1, 'DD Mon YYYY') FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT TO_DATE(e.text1) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
