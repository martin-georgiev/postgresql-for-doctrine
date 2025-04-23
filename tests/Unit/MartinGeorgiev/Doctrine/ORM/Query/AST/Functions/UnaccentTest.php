<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent;

class UnaccentTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Unaccent('UNACCENT');
    }

    protected function getStringFunctions(): array
    {
        return [
            'UNACCENT' => Unaccent::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT unaccent(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            "SELECT unaccent('unaccent', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT UNACCENT(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf("SELECT UNACCENT('unaccent', e.text1) FROM %s e", ContainsTexts::class),
        ];
    }

    /**
     * @test
     */
    public function throws_exception_when_too_many_arguments_given(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT UNACCENT('dict', e.text1, 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
