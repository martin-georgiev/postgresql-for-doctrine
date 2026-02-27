<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PlaintoTsquery;
use PHPUnit\Framework\Attributes\Test;

class PlaintoTsqueryTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new PlaintoTsquery('PLAINTO_TSQUERY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'PLAINTO_TSQUERY' => PlaintoTsquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts plain text to tsquery' => 'SELECT plainto_tsquery(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts plain text to tsquery with config' => "SELECT plainto_tsquery('english', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts plain text to tsquery' => \sprintf('SELECT PLAINTO_TSQUERY(e.text1) FROM %s e', ContainsTexts::class),
            'converts plain text to tsquery with config' => \sprintf("SELECT PLAINTO_TSQUERY('english', e.text1) FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT PLAINTO_TSQUERY('english', e.text1, 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
