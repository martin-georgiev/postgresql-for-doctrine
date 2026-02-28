<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsqueryPhrase;
use PHPUnit\Framework\Attributes\Test;

class TsqueryPhraseTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new TsqueryPhrase('TSQUERY_PHRASE');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TSQUERY_PHRASE' => TsqueryPhrase::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'combines two text fields into phrase query' => 'SELECT tsquery_phrase(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'combines two text fields with explicit distance' => 'SELECT tsquery_phrase(c0_.text1, c0_.text2, 2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'combines two text fields into phrase query' => \sprintf('SELECT TSQUERY_PHRASE(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'combines two text fields with explicit distance' => \sprintf('SELECT TSQUERY_PHRASE(e.text1, e.text2, 2) FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf('SELECT TSQUERY_PHRASE(e.text1, e.text2, 2, 3) FROM %s e', ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
