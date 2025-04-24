<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;

class ToTsqueryTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new ToTsquery('TO_TSQUERY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_tsquery(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT to_tsquery(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
            "SELECT to_tsquery('english', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT TO_TSQUERY(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT TO_TSQUERY(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
            \sprintf("SELECT TO_TSQUERY('english', e.text1) FROM %s e", ContainsTexts::class),
        ];
    }

    /**
     * @test
     */
    public function throws_exception_when_too_many_arguments_given(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT TO_TSQUERY('english', e.text1, 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
