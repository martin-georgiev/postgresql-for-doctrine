<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PhrasetoTsquery;
use PHPUnit\Framework\Attributes\Test;

class PhrasetoTsqueryTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new PhrasetoTsquery('PHRASETO_TSQUERY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'PHRASETO_TSQUERY' => PhrasetoTsquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts phrase to tsquery' => 'SELECT phraseto_tsquery(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts phrase to tsquery with config' => "SELECT phraseto_tsquery('english', c0_.text1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts phrase to tsquery' => \sprintf('SELECT PHRASETO_TSQUERY(e.text1) FROM %s e', ContainsTexts::class),
            'converts phrase to tsquery with config' => \sprintf("SELECT PHRASETO_TSQUERY('english', e.text1) FROM %s e", ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT PHRASETO_TSQUERY('english', e.text1, 'extra') FROM %s e", ContainsTexts::class);
        $this->assertSqlFromDql('', $dql);
    }
}
